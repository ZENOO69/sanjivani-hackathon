<?php
/**
 * FASAL - Live Disaster Recovery & Blackout Simulation API
 */

header('Content-Type: application/json; charset=UTF-8');
define('FASAL_ROOT', dirname(__DIR__));

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/backup.php';
require_once __DIR__ . '/../includes/blackout_engine.php';

$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true) ?: $_POST;

$action = isset($_GET['action']) ? $_GET['action'] : (isset($input['action']) ? $input['action'] : 'status');

if ($action === 'status') {
    $status = BlackoutEngine::getIntegrityStatus();
    $backups = BackupManager::listBackups();
    echo json_encode(array(
        'success' => true,
        'integrity' => $status,
        'backups' => $backups,
    ));
    exit;
}

if ($action === 'simulate_blackout') {
    // In-flight sample action
    $sampleEquipment = array(
        'table' => 'machinery_listings',
        'data' => array(
            'equipment_name' => HybridCrypto::encrypt('Mahindra 575 DI Tractor + Seed Drill (In-Flight Transaction)'),
            'owner_name'     => HybridCrypto::encrypt('Kisan Vikas Samiti (विकास समिती)'),
            'owner_phone'    => HybridCrypto::encrypt('+91 94220 99887'),
            'location'       => HybridCrypto::encrypt('Kopargaon Center'),
            'hourly_rate'    => HybridCrypto::encrypt('₹950 / Hour'),
            'status'         => HybridCrypto::encrypt('Available (Recovered In-Flight)'),
        )
    );

    $result = BlackoutEngine::simulateBlackout($sampleEquipment);
    echo json_encode($result);
    exit;
}

if ($action === 'auto_heal') {
    $result = BlackoutEngine::autoHealAndReplay();
    echo json_encode($result);
    exit;
}

if ($action === 'create_backup') {
    $note = isset($input['note']) ? Security::sanitizeString($input['note']) : 'Manual Snapshot';
    $result = BackupManager::createBackup(true, $note);
    echo json_encode($result);
    exit;
}

if ($action === 'restore_backup') {
    $filename = isset($input['filename']) ? basename($input['filename']) : '';
    if (empty($filename)) {
        echo json_encode(array('success' => false, 'error' => 'Filename required'));
        exit;
    }
    $result = BackupManager::restoreBackup($filename);
    echo json_encode($result);
    exit;
}

echo json_encode(array('success' => false, 'error' => 'Invalid action'));
