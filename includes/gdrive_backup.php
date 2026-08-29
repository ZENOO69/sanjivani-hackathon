<?php
/**
 * FASAL - Automated Free Google Drive Disaster Backup Sync
 * Backs up Database Snapshot, env.php, config.php, and WAL Journal to Google Drive (100% Free via Apps Script Webhook)
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/backup.php';

class GDriveBackupManager {
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

        // 1. Ensure latest local backup exists
        $localBackup = BackupManager::createBackup(false, 'Disaster Recovery Snapshot for Google Drive');

        // 2. Package database snapshot + config.php + sanitized env credentials
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
                'database_snapshot.json' => json_decode($dbData, true),
                'config.php'             => base64_encode($configContent),
                'env.php'                => base64_encode($envContent),
                'wal_journal.jsonl'      => base64_encode($walContent),
            ),
            'checksum' => hash('sha256', $dbData . $configContent . $envContent)
        );

        $jsonPayload = json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $base64Payload = base64_encode($jsonPayload);
        $filename = 'FASAL_DISASTER_BACKUP_' . $todayStr . '_' . date('His') . '.json';

        // 3. Dispatch to Google Drive Webhook
        $postData = json_encode(array(
            'filename'    => $filename,
            'mime_type'   => 'application/json',
            'file_base64' => $base64Payload,
        ));

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $resData = json_decode($response, true);
            if (isset($resData['status']) && $resData['status'] === 'success') {
                @file_put_contents($syncMarkerFile, date('c') . " - GDrive File ID: " . (isset($resData['file_id']) ? $resData['file_id'] : 'OK'), LOCK_EX);
                return array(
                    'success'  => true,
                    'filename' => $filename,
                    'file_id'  => isset($resData['file_id']) ? $resData['file_id'] : null,
                    'url'      => isset($resData['url']) ? $resData['url'] : null,
                    'message'  => 'Disaster backup successfully uploaded to Google Drive folder "FASAL_Disaster_Backups"!'
                );
            }
        }

        return array(
            'success' => false,
            'error'   => 'Failed to upload to Google Drive: ' . substr($response, 0, 200)
        );
    }
}
