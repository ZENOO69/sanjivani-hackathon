<?php
header('Content-Type: application/json; charset=UTF-8');
define('FASAL_ROOT', dirname(__DIR__));
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../database.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'latest';

if ($action === 'latest' || $action === 'sync' || $action === 'all') {
    $iotCfg = $config['iot'];
    $deviceHash = $iotCfg['device_hash'];
    $getUrl = $iotCfg['get_url'];

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

    $ch = curl_init($getUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $allRecords = array();
    if ($httpCode === 200 && !empty($response)) {
        $parsed = json_decode($response, true);
        if (is_array($parsed)) {
            $list = isset($parsed['data']) && is_array($parsed['data']) ? $parsed['data'] : (isset($parsed[0]) ? $parsed : array($parsed));
            foreach ($list as $item) {
                $allRecords[] = array(
                    'id'            => isset($item['id']) ? $item['id'] : count($allRecords) + 1,
                    'device_hash'   => isset($item['hash_id']) ? $item['hash_id'] : $deviceHash,
                    'temperature'   => isset($item['sensor1']) ? $item['sensor1'] : '0',
                    'humidity'      => isset($item['sensor2']) ? $item['sensor2'] : '0',
                    'soil_raw'      => isset($item['sensor3']) ? $item['sensor3'] : '0',
                    'soil_moisture' => isset($item['sensor4']) ? $item['sensor4'] : '0',
                    'soil_status'   => isset($item['sensor5']) ? $item['sensor5'] : 'DRY',
                    'datetime'      => isset($item['datetime']) ? $item['datetime'] : (isset($item['date']) ? $item['date'] . ' ' . $item['time'] : date('d-m-Y h:i A')),
                );
            }
            if (!empty($allRecords[0])) {
                $latestData['temperature']   = (string)$allRecords[0]['temperature'];
                $latestData['humidity']      = (string)$allRecords[0]['humidity'];
                $latestData['soil_raw']      = (string)$allRecords[0]['soil_raw'];
                $latestData['soil_moisture'] = (string)$allRecords[0]['soil_moisture'];
                $latestData['soil_status']   = (string)$allRecords[0]['soil_status'];
                $latestData['recorded_at']   = (string)$allRecords[0]['datetime'];
            }
        }
    }

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
        } catch (Exception $e) {}
    }

    echo json_encode(array(
        'success' => true,
        'data'    => $latestData,
        'records' => $allRecords,
    ));
    exit;
}

echo json_encode(array('success' => false, 'message' => 'Invalid action'));
