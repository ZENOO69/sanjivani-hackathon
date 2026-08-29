<?php
/**
 * ====================================================================
 * FASAL - Master Configuration File
 * ====================================================================
 * Unified Farmer Decision-Intelligence & Advisory Platform
 * Handles Google OAuth, Gemini AI, Weather API, SMTP, IoT & Database
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', __DIR__);
}

// Start Secure Session with extended lifetime
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 Days persistent session
    @ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
    @session_start();
}

$hostName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
$isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$proto = $isHttps ? 'https' : 'http';
$baseUrl = $proto . '://' . $hostName . rtrim($scriptName, '/\\');

return array(
    // ----------------------------------------------------------------
    // 1. Application Branding & Metadata
    // ----------------------------------------------------------------
    'app' => array(
        'name'        => 'FASAL',
        'tagline'     => 'स्मार्ट शेती, समृद्ध शेतकरी • Smart Farming Decision Platform',
        'footer_text' => '© ' . date('Y') . ' FASAL - Unified Farmer Decision-Intelligence Platform. Made with ❤️ for Maharashtra Farmers.',
        'version'     => '1.0.0-Release',
        'base_url'    => $baseUrl,
        'default_lang'=> 'mr', // mr = Marathi (मराठी), hi = Hindi (हिंदी), en = English
    ),

    // ----------------------------------------------------------------
    // 2. Farm Location Settings (Default: Kopargaon / Ahmednagar, MH)
    // ----------------------------------------------------------------
    'farm_location' => array(
        'region_name' => 'Kopargaon, Ahmednagar (कोपरगाव, अहिल्यानगर)',
        'state'       => 'Maharashtra',
        'latitude'    => 19.8917,
        'longitude'   => 74.4789,
        'elevation'   => 493, // meters
    ),

    // ----------------------------------------------------------------
    // 3. MySQL Database Configuration (Self-Migrating)
    // ----------------------------------------------------------------
    'database' => array(
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'fasal_agritech',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ),

    // ----------------------------------------------------------------
    // 4. Hybrid Cryptography Keys (AES-256-GCM + HMAC SHA-256)
    // ----------------------------------------------------------------
    'crypto' => array(
        // 32-byte secret encryption key
        'master_key' => 'f4s4l_k0p4rg40n_2026_s3cur1ty_k3y_#99',
        // Salt for blind indexing / searchable HMAC hashes
        'blind_index_salt' => 's4lt_k0p4rg40n_h4ck4th0n_2026',
    ),

    // ----------------------------------------------------------------
    // 5. Google OAuth 2.0 API Configuration
    // ----------------------------------------------------------------
    // Setup at: https://console.cloud.google.com/apis/credentials
    'google_oauth' => array(
        'client_id'     => 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri'  => $baseUrl . '/auth?action=google_callback',
    ),

    // ----------------------------------------------------------------
    // 6. Google Gemini AI API Configuration
    // ----------------------------------------------------------------
    // Get FREE API Key at: https://aistudio.google.com/
    'gemini_api' => array(
        'api_key'  => 'YOUR_GEMINI_API_KEY_HERE',
        'model'    => 'gemini-1.5-flash',
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    ),

    // ----------------------------------------------------------------
    // 7. Weather API Configuration (100% Free - Open-Meteo & Fallbacks)
    // ----------------------------------------------------------------
    // Open-Meteo requires NO API KEY and provides hyper-local agricultural data
    'weather_api' => array(
        'provider'    => 'open-meteo',
        'endpoint'    => 'https://api.open-meteo.com/v1/forecast',
        'openweather_key' => '', // Optional fallback key
    ),

    // ----------------------------------------------------------------
    // 8. SMTP Configuration (Google Gmail App Password)
    // ----------------------------------------------------------------
    // Generate App Password: https://myaccount.google.com/apppasswords
    'smtp' => array(
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'secure'     => 'tls', // 'tls' or 'ssl'
        'auth'       => true,
        'username'   => 'your-email@gmail.com',
        'password'   => 'your-16-char-app-password',
        'from_email' => 'no-reply@fasal-agri.in',
        'from_name'  => 'FASAL Farmer Advisory',
    ),

    // ----------------------------------------------------------------
    // 9. IoT ESP8266 Live Telemetry Endpoints
    // ----------------------------------------------------------------
    'iot' => array(
        'device_hash' => 'KOPARGAON_ESP8266_001',
        'get_url'     => 'https://www.ashishvegan.com/apps/kopargaon/get-data.php',
        'post_url'    => 'https://www.ashishvegan.com/apps/kopargaon/post-data.php',
        'sync_interval_seconds' => 30,
    ),

    // ----------------------------------------------------------------
    // 10. Maharashtra APMC Mandi Data Sources
    // ----------------------------------------------------------------
    'mandi' => array(
        'primary_market'  => 'Kopargaon APMC (कोपरगाव कृषी उत्पन्न बाजार समिती)',
        'nearby_markets'  => array('Rahata', 'Lasalgaon', 'Shrirampur', 'Yeola', 'Nashik', 'Nagpur', 'Akola'),
        'tracked_crops'   => array('Cotton (कापूस)', 'Onion (कांदा)', 'Oranges (संत्रा)', 'Sugarcane (ऊस)', 'Soybean (सोयाबीन)', 'Pomegranate (डाळिंब)', 'Wheat (गहू)', 'Maize (मका)'),
    ),
);
