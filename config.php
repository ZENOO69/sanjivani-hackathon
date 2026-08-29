<?php
if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', __DIR__);
}

$env = file_exists(__DIR__ . '/env.php') ? (include __DIR__ . '/env.php') : array();

// Persistent Secure Session setup
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
    @ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
    @session_start();
}

$isHttps = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1))
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$proto = $isHttps ? 'https' : 'http';
$hostName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'thermalstability.space';
$scriptDir = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
$scriptClean = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/\\');
$baseUrl = $proto . '://' . $hostName . $scriptClean;

$defaultRedirectUri = $baseUrl . '/auth?action=google_callback';

return array(
    'app' => array(
        'name'        => 'FASAL',
        'tagline'     => 'स्मार्ट शेती, समृद्ध शेतकरी • Smart Farming Decision Platform',
        'footer_text' => '© ' . date('Y') . ' FASAL - Unified Farmer Decision-Intelligence Platform. Made with ❤️ for Maharashtra Farmers.',
        'version'     => '1.0.0-Release',
        'base_url'    => $baseUrl,
        'default_lang'=> 'mr',
    ),
    'farm_location' => array(
        'region_name' => 'Kopargaon, Ahmednagar (कोपरगाव, अहिल्यानगर)',
        'state'       => 'Maharashtra',
        'latitude'    => 19.9015464,
        'longitude'   => 74.4921227,
        'elevation'   => 493,
    ),
    'database' => array(
        'host'     => isset($env['db_host']) ? $env['db_host'] : '127.0.0.1',
        'port'     => isset($env['db_port']) ? $env['db_port'] : 3306,
        'dbname'   => isset($env['db_name']) ? $env['db_name'] : 'u155978661_sanjivani',
        'username' => isset($env['db_user']) ? $env['db_user'] : 'u155978661_sanjivani',
        'password' => isset($env['db_pass']) ? $env['db_pass'] : '',
        'charset'  => 'utf8mb4',
    ),
    'crypto' => array(
        'master_key' => 'f4s4l_k0p4rg40n_2026_s3cur1ty_k3y_#99',
        'blind_index_salt' => 's4lt_k0p4rg40n_h4ck4th0n_2026',
    ),
    'google_oauth' => array(
        'client_id'     => isset($env['google_client_id']) ? $env['google_client_id'] : 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
        'client_secret' => isset($env['google_client_secret']) ? $env['google_client_secret'] : 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri'  => isset($env['google_redirect_uri']) ? $env['google_redirect_uri'] : $defaultRedirectUri,
    ),
    'gemini_api' => array(
        'api_key'  => isset($env['gemini_api_key']) ? $env['gemini_api_key'] : 'YOUR_GEMINI_API_KEY_HERE',
        'model'    => isset($env['gemini_model']) ? $env['gemini_model'] : 'gemini-1.5-flash',
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    ),
    'weather_api' => array(
        'provider'    => 'open-meteo',
        'endpoint'    => 'https://api.open-meteo.com/v1/forecast',
        'openweather_key' => '',
    ),
    'smtp' => array(
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'secure'     => 'tls',
        'auth'       => true,
        'username'   => isset($env['smtp_user']) ? $env['smtp_user'] : 'your-email@gmail.com',
        'password'   => isset($env['smtp_pass']) ? $env['smtp_pass'] : 'your-app-password',
        'from_email' => isset($env['smtp_from']) ? $env['smtp_from'] : 'no-reply@fasal-agri.in',
        'from_name'  => 'FASAL Farmer Advisory',
    ),
    'iot' => array(
        'device_hash' => 'KOPARGAON_ESP8266_001',
        'get_url'     => 'https://www.ashishvegan.com/apps/kopargaon/get-data.php',
        'post_url'    => 'https://www.ashishvegan.com/apps/kopargaon/post-data.php',
        'sync_interval_seconds' => 30,
    ),
    'mandi' => array(
        'primary_market'  => 'Kopargaon APMC (कोपरगाव कृषी उत्पन्न बाजार समिती)',
        'nearby_markets'  => array('Rahata', 'Lasalgaon', 'Shrirampur', 'Yeola', 'Nashik', 'Nagpur', 'Akola'),
        'tracked_crops'   => array('Cotton (कापूस)', 'Onion (कांदा)', 'Oranges (संत्रा)', 'Sugarcane (ऊस)', 'Soybean (सोयाबीन)', 'Pomegranate (डाळिंब)', 'Wheat (गहू)', 'Maize (मका)'),
    ),
);
