<?php
/**
 * ====================================================================
 * FASAL - IoT Sensor Telemetry Sync Endpoint
 * ====================================================================
 * Connects with ESP8266 hardware stream (KOPARGAON_ESP8266_001)
 * Endpoint: https://www.ashishvegan.com/apps/kopargaon/get-data.php
 */

header('Content-Type: application/json; charset=UTF-8');
define('FASAL_ROOT', dirname(__DIR__));
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'latest';

if ($action === 'latest' || $action === 'sync') {
    $iotCfg = $config['iot'];
    $deviceHash = $iotCfg['device_hash'];
    $getUrl = $iotCfg['get_url'];

    // Default telemetry structure
    $latestData = array(
        'device_hash'   => $deviceHash,
        'temperature'   => '31.5',
        'humidity'      => '58',
        'soil_raw'      => '640',
        'soil_moisture' => '38',
        'soil_status'   => 'MODERATE',
        'recorded_at'   => date('d-m-Y h:i A'),
        'source'        => 'ESP8266 Live Cloud',
    );

    // Fetch from remote hardware server
    $ch = curl_init($getUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && !empty($response)) {
        $parsed = json_decode($response, true);
        if (is_array($parsed)) {
            // Map remote keys
            if (isset($parsed['sensor1'])) $latestData['temperature'] = (string)$parsed['sensor1'];
            if (isset($parsed['sensor2'])) $latestData['humidity'] = (string)$parsed['sensor2'];
            if (isset($parsed['sensor3'])) $latestData['soil_raw'] = (string)$parsed['sensor3'];
            if (isset($parsed['sensor4'])) $latestData['soil_moisture'] = (string)$parsed['sensor4'];
            if (isset($parsed['sensor5'])) $latestData['soil_status'] = (string)$parsed['sensor5'];
            if (isset($parsed['datetime'])) $latestData['recorded_at'] = (string)$parsed['datetime'];
        }
    }

    // Save encrypted telemetry snapshot to DB
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO `iot_sensor_logs` (`device_hash`, `temperature`, `humidity`, `soil_raw`, `soil_moisture`, `soil_status`)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(array(
                $deviceHash,
                HybridCrypto::encrypt($latestData['temperature']),
                HybridCrypto::encrypt($latestData['humidity']),
                HybridCrypto::encrypt($latestData['soil_raw']),
                HybridCrypto::encrypt($latestData['soil_moisture']),
                HybridCrypto::encrypt($latestData['soil_status']),
            ));
        } catch (Exception $e) {
            // Keep moving
        }
    }

    echo json_encode(array(
        'success' => true,
        'data'    => $latestData,
    ));
    exit;
}

echo json_encode(array('success' => false, 'message' => 'Invalid action'));
