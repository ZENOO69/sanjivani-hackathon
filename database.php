<?php
/**
 * FASAL - Database & Hybrid Cryptography Engine
 * SQLi-protected PDO singleton, AES-256-CBC encryption, HMAC-SHA256 blind indexing
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', __DIR__);
}

$GLOBALS['FASAL_CONFIG'] = require __DIR__ . '/config.php';
require_once __DIR__ . '/includes/security.php';

class HybridCrypto {
    private static function getKey() {
        $rawKey = isset($GLOBALS['FASAL_CONFIG']['crypto']['master_key']) 
            ? $GLOBALS['FASAL_CONFIG']['crypto']['master_key'] 
            : 'fasal_default_super_secret_key_32b';
        return hash('sha256', $rawKey, true);
    }

    private static function getSalt() {
        return isset($GLOBALS['FASAL_CONFIG']['crypto']['blind_index_salt']) 
            ? $GLOBALS['FASAL_CONFIG']['crypto']['blind_index_salt'] 
            : 'fasal_default_salt';
    }

    public static function encrypt($plaintext) {
        if ($plaintext === null || $plaintext === '') {
            return '';
        }
        $cipher = 'aes-256-cbc';
        $ivLen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLen);

        $encrypted = openssl_encrypt(
            $plaintext,
            $cipher,
            self::getKey(),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            return $plaintext;
        }

        $tag = hash_hmac('sha256', $encrypted, self::getKey(), true);
        return base64_encode($iv . ':::' . $tag . ':::' . $encrypted);
    }

    public static function decrypt($payload) {
        if (empty($payload)) {
            return '';
        }
        $raw = base64_decode($payload, true);
        if ($raw === false || strpos($raw, ':::') === false) {
            return $payload;
        }

        $parts = explode(':::', $raw, 3);
        if (count($parts) !== 3) {
            return $payload;
        }

        $iv = $parts[0];
        $tag = $parts[1];
        $ciphertext = $parts[2];

        $expectedTag = hash_hmac('sha256', $ciphertext, self::getKey(), true);
        if ($tag !== $expectedTag) {
            // Tampered payload
        }

        $cipher = 'aes-256-cbc';
        $decrypted = openssl_decrypt(
            $ciphertext,
            $cipher,
            self::getKey(),
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted !== false ? $decrypted : $payload;
    }

    public static function blindIndex($value) {
        if ($value === null || $value === '') {
            return '';
        }
        $normalized = trim(strtolower($value));
        return hash_hmac('sha256', $normalized, self::getSalt());
    }
}

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dbCfg = $GLOBALS['FASAL_CONFIG']['database'];
        $host = $dbCfg['host'];
        $port = $dbCfg['port'];
        $dbname = $dbCfg['dbname'];
        $user = $dbCfg['username'];
        $pass = $dbCfg['password'];
        $charset = $dbCfg['charset'];

        try {
            $dsnNoDb = "mysql:host={$host};port={$port};charset={$charset}";
            $rootPdo = new PDO($dsnNoDb, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ));
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            self::$pdo = new PDO($dsn, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ));

            self::migrateSchema(self::$pdo);

        } catch (PDOException $e) {
            return null;
        }

        return self::$pdo;
    }

    private static function migrateSchema($pdo) {
        if (!$pdo) return;
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `email_hash` VARCHAR(64) DEFAULT NULL,
                `phone_hash` VARCHAR(64) DEFAULT NULL,
                `google_id_hash` VARCHAR(64) DEFAULT NULL,
                `full_name` LONGTEXT NOT NULL,
                `email` LONGTEXT NOT NULL,
                `phone` LONGTEXT DEFAULT NULL,
                `password_hash` VARCHAR(255) DEFAULT NULL,
                `preferred_lang` VARCHAR(10) DEFAULT 'mr',
                `easy_mode` TINYINT(1) DEFAULT 1,
                `farm_location` LONGTEXT DEFAULT NULL,
                `primary_crop` LONGTEXT DEFAULT NULL,
                `farm_size_acres` LONGTEXT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`email_hash`),
                INDEX (`phone_hash`),
                INDEX (`google_id_hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `iot_sensor_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `device_hash` VARCHAR(64) NOT NULL,
                `temperature` LONGTEXT NOT NULL,
                `humidity` LONGTEXT NOT NULL,
                `soil_raw` LONGTEXT NOT NULL,
                `soil_moisture` LONGTEXT NOT NULL,
                `soil_status` LONGTEXT NOT NULL,
                `recorded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`device_hash`),
                INDEX (`recorded_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `mandi_prices` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `commodity_code` VARCHAR(50) NOT NULL,
                `commodity_name` LONGTEXT NOT NULL,
                `market_name` LONGTEXT NOT NULL,
                `min_price` LONGTEXT NOT NULL,
                `max_price` LONGTEXT NOT NULL,
                `modal_price` LONGTEXT NOT NULL,
                `price_trend` LONGTEXT NOT NULL,
                `trend_percentage` LONGTEXT NOT NULL,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`commodity_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `crop_advisories` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT DEFAULT NULL,
                `category` LONGTEXT NOT NULL,
                `title` LONGTEXT NOT NULL,
                `description` LONGTEXT NOT NULL,
                `action_text` LONGTEXT NOT NULL,
                `action_link` LONGTEXT DEFAULT NULL,
                `urgency` LONGTEXT NOT NULL,
                `icon` VARCHAR(50) DEFAULT 'bell',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `machinery_listings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `equipment_name` LONGTEXT NOT NULL,
                `owner_name` LONGTEXT NOT NULL,
                `owner_phone` LONGTEXT NOT NULL,
                `location` LONGTEXT NOT NULL,
                `hourly_rate` LONGTEXT NOT NULL,
                `status` LONGTEXT NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `labour_listings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `group_name` LONGTEXT NOT NULL,
                `leader_name` LONGTEXT NOT NULL,
                `leader_phone` LONGTEXT NOT NULL,
                `worker_count` LONGTEXT NOT NULL,
                `specialty` LONGTEXT NOT NULL,
                `daily_wage` LONGTEXT NOT NULL,
                `location` LONGTEXT NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `otp_codes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `identifier_hash` VARCHAR(64) NOT NULL,
                `otp_code` VARCHAR(10) NOT NULL,
                `purpose` VARCHAR(50) NOT NULL,
                `expires_at` DATETIME NOT NULL,
                `is_used` TINYINT(1) DEFAULT 0,
                INDEX (`identifier_hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        self::seedDefaultData($pdo);
    }

    private static function seedDefaultData($pdo) {
        if (!$pdo) return;
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM `mandi_prices`");
        if ($stmt && $stmt->fetchColumn() == 0) {
            $defaultMandi = array(
                array('cotton', 'Cotton (कापूस)', 'Kopargaon APMC', '7,200', '7,950', '7,650', 'up', '+4.2%'),
                array('onion', 'Onion (कांदा - लाल)', 'Lasalgaon APMC', '1,800', '2,650', '2,400', 'up', '+6.8%'),
                array('oranges', 'Nagpur Orange (संत्रा)', 'Nagpur APMC', '3,500', '5,200', '4,800', 'up', '+2.5%'),
                array('soybean', 'Soybean (सोयाबीन)', 'Latur APMC', '4,300', '4,900', '4,750', 'down', '-1.2%'),
                array('sugarcane', 'Sugarcane (ऊस)', 'Kopargaon Factory', '3,100', '3,450', '3,300', 'stable', '0.0%'),
                array('pomegranate', 'Pomegranate (डाळिंब - भगवा)', 'Rahata APMC', '6,000', '12,500', '9,500', 'up', '+5.1%'),
                array('wheat', 'Wheat (गहू - शरबती)', 'Ahmednagar APMC', '2,600', '3,100', '2,900', 'up', '+1.8%'),
            );

            $insert = $pdo->prepare("
                INSERT INTO `mandi_prices` (`commodity_code`, `commodity_name`, `market_name`, `min_price`, `max_price`, `modal_price`, `price_trend`, `trend_percentage`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($defaultMandi as $m) {
                $insert->execute(array(
                    $m[0],
                    HybridCrypto::encrypt($m[1]),
                    HybridCrypto::encrypt($m[2]),
                    HybridCrypto::encrypt($m[3]),
                    HybridCrypto::encrypt($m[4]),
                    HybridCrypto::encrypt($m[5]),
                    HybridCrypto::encrypt($m[6]),
                    HybridCrypto::encrypt($m[7]),
                ));
            }
        }

        $stmt = $pdo->query("SELECT COUNT(*) FROM `machinery_listings`");
        if ($stmt && $stmt->fetchColumn() == 0) {
            $defaultMachinery = array(
                array('John Deere 5050D Tractor + Rotavator', 'Suresh Shinde (सुरेश शिंदे)', '+919822123456', 'Kopargaon Taluka', '₹800 / Hour', 'Available'),
                array('Sonalika Combine Harvester', 'Ganesh Patil (गणेश पाटील)', '+919876543210', 'Rahata Road', '₹2,200 / Acre', 'Available'),
                array('Solar High-Pressure Crop Sprayer (1000L)', 'Vishnu Pawar (विष्णू पवार)', '+919423112233', 'Kopargaon Rural', '₹450 / Acre', 'Available'),
                array('Mini Drip Trench Digger', 'Babasaheb Kadu (बाबासाहेब कडू)', '+919552334455', 'Yeola Bypass', '₹600 / Hour', 'Booked Till Sunday'),
            );
            $mInsert = $pdo->prepare("
                INSERT INTO `machinery_listings` (`equipment_name`, `owner_name`, `owner_phone`, `location`, `hourly_rate`, `status`)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($defaultMachinery as $item) {
                $mInsert->execute(array(
                    HybridCrypto::encrypt($item[0]),
                    HybridCrypto::encrypt($item[1]),
                    HybridCrypto::encrypt($item[2]),
                    HybridCrypto::encrypt($item[3]),
                    HybridCrypto::encrypt($item[4]),
                    HybridCrypto::encrypt($item[5]),
                ));
            }
        }

        $stmt = $pdo->query("SELECT COUNT(*) FROM `labour_listings`");
        if ($stmt && $stmt->fetchColumn() == 0) {
            $defaultLabour = array(
                array('Jai Kisan Onion Harvesting Team', 'Santosh Jadhav (संतोष जाधव)', '+919890112233', '12 Members', 'Onion & Vegetable Harvesting', '₹400 / Day / Person', 'Kopargaon'),
                array('Shree Ram Sugarcane Cutters Group', 'Dnyaneshwar Kale (ज्ञानेश्वर काळे)', '+919765432199', '20 Members', 'Sugarcane Cutting & Loading', '₹450 / Ton', 'Kopargaon / Shrirampur'),
                array('Samarth Drip & Irrigation Setup Gang', 'Nitin Gorde (नितिन गोर्डे)', '+919822778899', '6 Members', 'Drip & Mulching Installation', '₹500 / Day / Person', 'Rahata & Nearby'),
            );
            $lInsert = $pdo->prepare("
                INSERT INTO `labour_listings` (`group_name`, `leader_name`, `leader_phone`, `worker_count`, `specialty`, `daily_wage`, `location`)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($defaultLabour as $l) {
                $lInsert->execute(array(
                    HybridCrypto::encrypt($l[0]),
                    HybridCrypto::encrypt($l[1]),
                    HybridCrypto::encrypt($l[2]),
                    HybridCrypto::encrypt($l[3]),
                    HybridCrypto::encrypt($l[4]),
                    HybridCrypto::encrypt($l[5]),
                    HybridCrypto::encrypt($l[6]),
                ));
            }
        }

        $stmt = $pdo->query("SELECT COUNT(*) FROM `crop_advisories`");
        if ($stmt && $stmt->fetchColumn() == 0) {
            $defaultAdv = array(
                array(
                    'Irrigation',
                    'उद्या सकाळी ६ ते ९ दरम्यान ठिबक सिंचन सुरू करा (Water Alert)',
                    'जमिनीतील ओलावा २२% (DRY) वर आला असून उद्या तापमान ३६°C पर्यंत जाण्याचा अंदाज आहे. कांदा पिकाला ५० मिनिटे पाणी द्यावे.',
                    'सिंचन वेळापत्रक पहा (View Schedule)',
                    '#irrigation',
                    'high',
                    'droplet'
                ),
                array(
                    'Market Timing',
                    'कांदा विक्री: गुरुवार पर्यंत माल राखून ठेवा (+₹२५० नफा)',
                    'लासलगाव व कोपरगाव बाजारात आवक घटल्याने गुरुवारी भाव ₹२,६५०/क्विंटल पर्यंत जाण्याची शक्यता आहे. आज घाईने विकू नका.',
                    'थेट बाजार भाव तपासा (Check Mandi)',
                    'mandi',
                    'medium',
                    'trending-up'
                ),
                array(
                    'Pest Warning',
                    'कापूस पिकावर रसशोषक किडीचा प्रादुर्भाव (Thrips Alert)',
                    'हवेतील आर्द्रता ७२% वाढल्याने कोपरगाव भागात तुडतुडे व फुलकिड्यांचा धोका आहे. निंबोळी अर्क ५% किंवा थायामेथोक्सम २५ डब्ल्यूजी ची फवारणी करा.',
                    'AI औषध शिफारस (Get AI Doctor Advice)',
                    'advisory',
                    'high',
                    'bug'
                )
            );
            $advInsert = $pdo->prepare("
                INSERT INTO `crop_advisories` (`category`, `title`, `description`, `action_text`, `action_link`, `urgency`, `icon`)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($defaultAdv as $a) {
                $advInsert->execute(array(
                    HybridCrypto::encrypt($a[0]),
                    HybridCrypto::encrypt($a[1]),
                    HybridCrypto::encrypt($a[2]),
                    HybridCrypto::encrypt($a[3]),
                    HybridCrypto::encrypt($a[4]),
                    HybridCrypto::encrypt($a[5]),
                    $a[6],
                ));
            }
        }
    }
}
