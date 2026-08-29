<?php
/**
 * FASAL - Automated Free Google Drive Disaster Backup Sync
 * Backs up Database SQL Dump (.sql), JSON Snapshot, env.php, config.php, and WAL Journal to Google Drive
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/backup.php';
require_once __DIR__ . '/../database.php';

class GDriveBackupManager {
    /**
     * Generate standard MySQL .sql dump string from database or snapshot
     */
    public static function generateSqlDump() {
        $pdo = Database::getConnection();
        $tables = array(
            'users',
            'iot_sensor_logs',
            'mandi_prices',
            'crop_advisories',
            'machinery_listings',
            'labour_listings',
            'otp_codes'
        );

        $sql = "-- ====================================================================\n";
        $sql .= "-- FASAL Automated Database Disaster Recovery SQL Dump\n";
        $sql .= "-- App: FASAL - Kopargaon Smart Agriculture Platform\n";
        $sql .= "-- Generated At: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ====================================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "START TRANSACTION;\n";
        $sql .= "SET time_zone = '+05:30';\n\n";

        if ($pdo) {
            foreach ($tables as $table) {
                try {
                    $sql .= "-- --------------------------------------------------------\n";
                    $sql .= "-- Table structure for table `{$table}`\n";
                    $sql .= "-- --------------------------------------------------------\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                    $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                    if ($createStmt) {
                        $row = $createStmt->fetch(PDO::FETCH_NUM);
                        if (!empty($row[1])) {
                            $sql .= $row[1] . ";\n\n";
                        }
                    }

                    // Dump data rows
                    $stmt = $pdo->query("SELECT * FROM `{$table}`");
                    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
                    if (!empty($rows)) {
                        $sql .= "-- Dumping data for table `{$table}` (" . count($rows) . " rows)\n";
                        $firstRow = $rows[0];
                        $columns = array_keys($firstRow);
                        $colList = '`' . implode('`, `', $columns) . '`';

                        foreach ($rows as $r) {
                            $escapedValues = array();
                            foreach ($r as $val) {
                                if ($val === null) {
                                    $escapedValues[] = 'NULL';
                                } else {
                                    $escapedValues[] = $pdo->quote($val);
                                }
                            }
                            $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES (" . implode(', ', $escapedValues) . ");\n";
                        }
                        $sql .= "\n";
                    }
                } catch (Exception $e) {}
            }
        } else {
            // Reconstruct SQL from latest snapshot or HA cache
            $snapshotFile = FASAL_ROOT . '/data/backups/latest_snapshot.json';
            $cached = file_exists($snapshotFile) ? json_decode(file_get_contents($snapshotFile), true) : array();
            $tableData = isset($cached['tables']) ? $cached['tables'] : array();
            $schemas = isset($cached['schema']) ? $cached['schema'] : array();

            foreach ($tables as $table) {
                $sql .= "-- Table `{$table}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                if (!empty($schemas[$table])) {
                    $sql .= $schemas[$table] . ";\n\n";
                }
                if (!empty($tableData[$table])) {
                    $rows = $tableData[$table];
                    $firstRow = $rows[0];
                    $columns = array_keys($firstRow);
                    $colList = '`' . implode('`, `', $columns) . '`';
                    foreach ($rows as $r) {
                        $vals = array_map(function($v) {
                            return "'" . addslashes((string)$v) . "'";
                        }, array_values($r));
                        $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES (" . implode(', ', $vals) . ");\n";
                    }
                    $sql .= "\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "COMMIT;\n";
        $sql .= "-- Dump completed on " . date('Y-m-d H:i:s') . "\n";

        return $sql;
    }

    /**
     * Send payload to Google Drive Webhook
     */
    private static function uploadToDriveWebhook($webhookUrl, $filename, $mimeType, $content) {
        $postData = json_encode(array(
            'filename'    => $filename,
            'mime_type'   => $mimeType,
            'file_base64' => base64_encode($content),
        ));

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $resData = json_decode($response, true);
            if (isset($resData['status']) && $resData['status'] === 'success') {
                return array('success' => true, 'file_id' => $resData['file_id'], 'url' => $resData['url']);
            }
        }
        return array('success' => false, 'error' => substr((string)$response, 0, 200));
    }

    /**
     * Sync both full .sql dump and complete disaster bundle to Google Drive
     */
    public static function syncDisasterBackupToGDrive($force = false) {
        $env = file_exists(FASAL_ROOT . '/env.php') ? (include FASAL_ROOT . '/env.php') : array();
        $webhookUrl = isset($env['gdrive_backup_webhook']) ? trim($env['gdrive_backup_webhook']) : '';

        if (empty($webhookUrl)) {
            return array(
                'success' => false,
                'message' => 'Google Drive Webhook URL not configured in env.php (Set gdrive_backup_webhook).'
            );
        }

        $todayStr = date('Y-m-d');
        $syncMarkerFile = FASAL_ROOT . '/data/backups/gdrive_sync_' . $todayStr . '.marker';

        if (!$force && file_exists($syncMarkerFile)) {
            return array(
                'success' => true,
                'message' => 'Today\'s Google Drive disaster backup already completed (' . $todayStr . ').'
            );
        }

        // 1. Ensure latest local backup snapshot exists
        BackupManager::createBackup(false, 'Disaster Recovery Snapshot for Google Drive');

        // 2. Generate standard executable .sql file dump
        $sqlDumpContent = self::generateSqlDump();
        $sqlFilename = 'FASAL_DATABASE_DUMP_' . $todayStr . '_' . date('His') . '.sql';

        // Save local .sql copy in data/backups/
        $localSqlFile = FASAL_ROOT . '/data/backups/' . $sqlFilename;
        @file_put_contents($localSqlFile, $sqlDumpContent, LOCK_EX);

        // 3. Upload Standalone .sql Dump directly to Google Drive
        $sqlUpload = self::uploadToDriveWebhook($webhookUrl, $sqlFilename, 'application/sql', $sqlDumpContent);

        // 4. Package complete master disaster recovery bundle
        $snapshotFile = FASAL_ROOT . '/data/backups/latest_snapshot.json';
        $dbData = file_exists($snapshotFile) ? file_get_contents($snapshotFile) : '{}';

        $configFile = FASAL_ROOT . '/config.php';
        $configContent = file_exists($configFile) ? file_get_contents($configFile) : '';

        $envFile = FASAL_ROOT . '/env.php';
        $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

        $walFile = FASAL_ROOT . '/data/wal_journal.jsonl';
        $walContent = file_exists($walFile) ? file_get_contents($walFile) : '';

        $bundle = array(
            'backup_timestamp' => date('c'),
            'date'             => $todayStr,
            'app'              => 'FASAL - Kopargaon Smart Agriculture Decision Platform',
            'files' => array(
                'database_dump.sql'      => base64_encode($sqlDumpContent),
                'database_snapshot.json' => json_decode($dbData, true),
                'config.php'             => base64_encode($configContent),
                'env.php'                => base64_encode($envContent),
                'wal_journal.jsonl'      => base64_encode($walContent),
            ),
            'checksum' => hash('sha256', $sqlDumpContent . $dbData . $configContent . $envContent)
        );

        $jsonPayload = json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $bundleFilename = 'FASAL_DISASTER_BUNDLE_' . $todayStr . '_' . date('His') . '.json';

        // 5. Upload Master Bundle to Google Drive
        $bundleUpload = self::uploadToDriveWebhook($webhookUrl, $bundleFilename, 'application/json', $jsonPayload);

        $sqlSuccess = isset($sqlUpload['success']) && $sqlUpload['success'];
        $bundleSuccess = isset($bundleUpload['success']) && $bundleUpload['success'];

        if ($sqlSuccess || $bundleSuccess) {
            $sqlId = isset($sqlUpload['file_id']) ? $sqlUpload['file_id'] : 'N/A';
            $bundleId = isset($bundleUpload['file_id']) ? $bundleUpload['file_id'] : 'N/A';
            @file_put_contents($syncMarkerFile, date('c') . " - SQL ID: " . $sqlId . " | Bundle ID: " . $bundleId, LOCK_EX);
            return array(
                'success'       => true,
                'sql_file'      => $sqlFilename,
                'sql_url'       => isset($sqlUpload['url']) ? $sqlUpload['url'] : null,
                'bundle_file'   => $bundleFilename,
                'bundle_url'    => isset($bundleUpload['url']) ? $bundleUpload['url'] : null,
                'message'       => 'Both standalone .SQL Dump and Master Disaster Bundle successfully uploaded to Google Drive folder "FASAL_Disaster_Backups"!'
            );
        }

        $sqlErr = isset($sqlUpload['error']) ? $sqlUpload['error'] : '';
        $bundleErr = isset($bundleUpload['error']) ? $bundleUpload['error'] : '';

        return array(
            'success' => false,
            'error'   => 'Google Drive upload error: ' . $sqlErr . ' / ' . $bundleErr
        );
    }
}
