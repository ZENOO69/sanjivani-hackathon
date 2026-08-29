<?php
if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/gdrive_backup.php';

class BackupManager {
    private static function getBackupDir() {
        $dir = FASAL_ROOT . '/data/backups';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function checkDailyBackupAuto() {
        $todayStr = date('Y-m-d');
        $backupDir = self::getBackupDir();
        $todayFile = $backupDir . '/daily_backup_' . $todayStr . '.json';

        $localResult = null;
        if (!file_exists($todayFile)) {
            $localResult = self::createBackup(false, 'Scheduled Daily Auto-Backup (' . $todayStr . ')');
        }

        // Trigger Google Drive sync if webhook configured
        @GDriveBackupManager::syncDisasterBackupToGDrive(false);

        return $localResult;
    }

    public static function createBackup($isManual = false, $note = '') {
        $pdo = Database::getConnection();
        $backupDir = self::getBackupDir();
        $dateStr = date('Y-m-d');
        $timeStr = date('H-i-s');
        $filename = ($isManual ? 'manual_backup_' : 'daily_backup_') . $dateStr . ($isManual ? '_' . $timeStr : '') . '.json';
        $targetPath = $backupDir . '/' . $filename;

        $tables = array(
            'users',
            'iot_sensor_logs',
            'mandi_prices',
            'crop_advisories',
            'machinery_listings',
            'labour_listings',
            'otp_codes'
        );

        $backupData = array(
            'metadata' => array(
                'app' => 'FASAL Farmer Advisory Platform',
                'backup_type' => $isManual ? 'MANUAL' : 'DAILY_AUTO',
                'created_at' => date('c'),
                'date' => $dateStr,
                'note' => !empty($note) ? $note : 'Automated Daily Snapshot',
                'version' => '1.0.0',
            ),
            'tables' => array(),
            'schema' => array(),
            'total_records' => 0,
        );

        $totalRecs = 0;
        if ($pdo) {
            foreach ($tables as $table) {
                try {
                    $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                    if ($createStmt) {
                        $row = $createStmt->fetch(PDO::FETCH_NUM);
                        $backupData['schema'][$table] = isset($row[1]) ? $row[1] : '';
                    }

                    $stmt = $pdo->query("SELECT * FROM `{$table}`");
                    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();
                    $backupData['tables'][$table] = $rows;
                    $totalRecs += count($rows);
                } catch (Exception $e) {
                    $backupData['tables'][$table] = array();
                }
            }
        } else {
            // HA Shadow cache fallback snapshot
            $cacheFile = FASAL_ROOT . '/data/ha_cache.json';
            $cached = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : array();
            foreach ($tables as $table) {
                $rows = isset($cached[$table]) ? $cached[$table] : array();
                $backupData['tables'][$table] = $rows;
                $totalRecs += count($rows);
            }
        }

        $backupData['total_records'] = $totalRecs;
        $backupData['checksum'] = hash('sha256', json_encode($backupData['tables']));

        $jsonPayload = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($targetPath, $jsonPayload, LOCK_EX);
        file_put_contents($backupDir . '/latest_snapshot.json', $jsonPayload, LOCK_EX);

        self::pruneOldBackups(30);

        return array(
            'success' => true,
            'filename' => $filename,
            'path' => $targetPath,
            'total_records' => $totalRecs,
            'checksum' => $backupData['checksum'],
            'created_at' => date('d-m-Y H:i:s'),
        );
    }

    public static function listBackups() {
        $backupDir = self::getBackupDir();
        $files = glob($backupDir . '/*.json');
        $list = array();

        if ($files) {
            foreach ($files as $file) {
                $base = basename($file);
                if ($base === 'latest_snapshot.json') continue;
                $raw = @file_get_contents($file);
                $data = json_decode($raw, true);
                $list[] = array(
                    'filename' => $base,
                    'size_kb' => round(filesize($file) / 1024, 2),
                    'created_at' => isset($data['metadata']['created_at']) ? date('d-m-Y H:i:s', strtotime($data['metadata']['created_at'])) : date('d-m-Y H:i:s', filemtime($file)),
                    'type' => isset($data['metadata']['backup_type']) ? $data['metadata']['backup_type'] : 'DAILY_AUTO',
                    'records' => isset($data['total_records']) ? $data['total_records'] : 0,
                    'note' => isset($data['metadata']['note']) ? $data['metadata']['note'] : 'Server Snapshot',
                );
            }
        }

        usort($list, function($a, $b) {
            return strcmp($b['filename'], $a['filename']);
        });

        return $list;
    }

    public static function restoreBackup($filename) {
        $backupDir = self::getBackupDir();
        $safeFile = basename($filename);
        $filePath = $backupDir . '/' . $safeFile;

        if (!file_exists($filePath)) {
            return array('success' => false, 'error' => 'Backup file not found.');
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (!$data || !isset($data['tables'])) {
            return array('success' => false, 'error' => 'Invalid backup payload.');
        }

        $pdo = Database::getConnection();
        if ($pdo) {
            try {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
                foreach ($data['tables'] as $table => $rows) {
                    $pdo->exec("TRUNCATE TABLE `{$table}`");
                    if (!empty($rows)) {
                        $firstRow = $rows[0];
                        $columns = array_keys($firstRow);
                        $colList = '`' . implode('`, `', $columns) . '`';
                        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

                        $stmt = $pdo->prepare("INSERT INTO `{$table}` ({$colList}) VALUES ({$placeholders})");
                        foreach ($rows as $row) {
                            $stmt->execute(array_values($row));
                        }
                    }
                }
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            } catch (Exception $e) {}
        }

        // Restore HA shadow cache as well
        $cacheFile = FASAL_ROOT . '/data/ha_cache.json';
        @file_put_contents($cacheFile, json_encode($data['tables'], JSON_PRETTY_PRINT), LOCK_EX);

        return array('success' => true, 'message' => 'Database successfully restored from ' . $safeFile);
    }

    private static function pruneOldBackups($days = 30) {
        $backupDir = self::getBackupDir();
        $files = glob($backupDir . '/*.json');
        $cutoff = time() - ($days * 86400);

        if ($files) {
            foreach ($files as $file) {
                if (basename($file) === 'latest_snapshot.json') continue;
                if (filemtime($file) < $cutoff) {
                    @unlink($file);
                }
            }
        }
    }
}

BackupManager::checkDailyBackupAuto();
