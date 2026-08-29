<?php
header('Content-Type: application/json; charset=UTF-8');
define('FASAL_ROOT', dirname(__DIR__));
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/security.php';

$lat = (float)(isset($_GET['lat']) ? $_GET['lat'] : $config['farm_location']['latitude']);
$lon = (float)(isset($_GET['lon']) ? $_GET['lon'] : $config['farm_location']['longitude']);

$weatherUrl = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,rain,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max&timezone=Asia%2FKolkata&forecast_days=7";

$ch = curl_init($weatherUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && !empty($response)) {
    $data = json_decode($response, true);
    if (!empty($data['current'])) {
        $cur = $data['current'];
        $daily = isset($data['daily']) ? $data['daily'] : array();

        $wCode = isset($cur['weather_code']) ? $cur['weather_code'] : 0;
        $conditionText = 'स्वच्छ आकाश (Clear Sky)';
        $icon = 'sun';

        if ($wCode >= 1 && $wCode <= 3) {
            $conditionText = 'अंशतः ढगाळ (Partly Cloudy)';
            $icon = 'cloud-sun';
        } elseif ($wCode >= 51 && $wCode <= 67) {
            $conditionText = 'हलका पाऊस (Light Rain)';
            $icon = 'cloud-drizzle';
        } elseif ($wCode >= 80 && $wCode <= 99) {
            $conditionText = 'जोरदार पाऊस / वादळ (Thunderstorm / Heavy Rain)';
            $icon = 'cloud-lightning';
        }

        echo json_encode(array(
            'success' => true,
            'source'  => 'Open-Meteo Live API',
            'location'=> $config['farm_location']['region_name'],
            'current' => array(
                'temperature'       => isset($cur['temperature_2m']) ? $cur['temperature_2m'] : 32,
                'feels_like'        => isset($cur['apparent_temperature']) ? $cur['apparent_temperature'] : 34,
                'humidity'          => isset($cur['relative_humidity_2m']) ? $cur['relative_humidity_2m'] : 55,
                'rain_mm'           => isset($cur['rain']) ? $cur['rain'] : 0.0,
                'wind_kmh'          => isset($cur['wind_speed_10m']) ? $cur['wind_speed_10m'] : 14,
                'weather_code'      => $wCode,
                'condition'         => $conditionText,
                'icon'              => $icon,
            ),
            'daily' => $daily,
        ));
        exit;
    }
}

echo json_encode(array(
    'success' => true,
    'source'  => 'Local Simulated Forecast',
    'location'=> $config['farm_location']['region_name'],
    'current' => array(
        'temperature'       => 32.5,
        'feels_like'        => 34.0,
        'humidity'          => 54,
        'rain_mm'           => 0.0,
        'wind_kmh'          => 12,
        'weather_code'      => 1,
        'condition'         => 'स्वच्छ व कोरडे हवामान (Sunny & Clear)',
        'icon'              => 'sun',
    ),
    'daily' => array(
        'time' => array(date('Y-m-d'), date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+2 day'))),
        'temperature_2m_max' => array(34, 35, 33),
        'temperature_2m_min' => array(21, 22, 20),
        'precipitation_probability_max' => array(5, 10, 15),
    )
));
