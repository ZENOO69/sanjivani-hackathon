<?php
/**
 * FASAL - Security Engine (SQLi, XSS, CSRF, DoS, DDoS Protections)
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

class Security {
    private static $rateLimitFile = null;
    private static $jailFile = null;

    public static function init() {
        self::$rateLimitFile = FASAL_ROOT . '/data/rate_limit.json';
        self::$jailFile = FASAL_ROOT . '/data/ip_jail.json';

        self::sendSecurityHeaders();
        self::enforceMaxPayload(2 * 1024 * 1024); // 2MB Limit
        self::enforceDDoSAndRateLimit();
        self::initCsrfToken();
    }

    // Send HTTP Security Headers
    public static function sendSecurityHeaders() {
        if (headers_sent()) return;
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net https://api.open-meteo.com https://generativelanguage.googleapis.com https://www.ashishvegan.com data: blob:;");
    }

    // Resolve Client IP behind Proxies & Cloudflare
    public static function getClientIp() {
        $headers = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '127.0.0.1';
    }

    // Enforce Maximum POST Payload to prevent memory exhaustion / DoS
    public static function enforceMaxPayload($maxBytes = 2097152) {
        $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
        if ($contentLength > $maxBytes) {
            http_response_code(413);
            die(json_encode(array('error' => 'Payload Too Large. Request rejected.')));
        }
    }

    // DDoS & Rate Limiting Guard
    public static function enforceDDoSAndRateLimit() {
        $ip = self::getClientIp();
        $now = time();

        // 1. Check IP Jail (Temporary Ban for DDoS Burst Offenders)
        $jailData = self::readJsonFile(self::$jailFile);
        if (isset($jailData[$ip])) {
            $banUntil = (int)$jailData[$ip]['banned_until'];
            if ($now < $banUntil) {
                http_response_code(429);
                header('Retry-After: ' . ($banUntil - $now));
                die(json_encode(array(
                    'error' => 'DDoS / Excessive Request Protection: IP temporarily restricted.',
                    'retry_in_seconds' => $banUntil - $now
                )));
            } else {
                unset($jailData[$ip]);
                self::writeJsonFile(self::$jailFile, $jailData);
            }
        }

        // 2. Sliding Window Rate Limiting (DoS Defense)
        $rateData = self::readJsonFile(self::$rateLimitFile);
        $window = 60; // 60 seconds
        $maxRequests = 120; // 120 requests/minute
        $burstWindow = 5; // 5 seconds
        $maxBurst = 25; // max 25 req in 5 sec

        if (!isset($rateData[$ip])) {
            $rateData[$ip] = array();
        }

        // Clean timestamps older than window
        $rateData[$ip] = array_values(array_filter($rateData[$ip], function($ts) use ($now, $window) {
            return ($now - $ts) < $window;
        }));

        $rateData[$ip][] = $now;

        // Check Burst
        $burstCount = 0;
        foreach ($rateData[$ip] as $ts) {
            if (($now - $ts) <= $burstWindow) {
                $burstCount++;
            }
        }

        if ($burstCount > $maxBurst) {
            // Put in Jail for 5 minutes
            $jailData[$ip] = array(
                'banned_until' => $now + 300,
                'reason' => 'DDoS Burst Exceeded (' . $burstCount . ' reqs in 5s)',
                'timestamp' => $now
            );
            self::writeJsonFile(self::$jailFile, $jailData);
            self::writeJsonFile(self::$rateLimitFile, $rateData);
            http_response_code(429);
            die(json_encode(array('error' => 'DDoS Burst Detected. IP restricted for 5 minutes.')));
        }

        if (count($rateData[$ip]) > $maxRequests) {
            self::writeJsonFile(self::$rateLimitFile, $rateData);
            http_response_code(429);
            die(json_encode(array('error' => 'Rate limit exceeded. Maximum 120 requests/minute allowed.')));
        }

        self::writeJsonFile(self::$rateLimitFile, $rateData);
    }

    // Bot Honeypot Trap Detection
    public static function checkHoneypot($fieldName = 'farm_security_code') {
        if (!empty($_POST[$fieldName])) {
            http_response_code(400);
            die(json_encode(array('error' => 'Bot submission trapped and discarded.')));
        }
    }

    // CSRF Token Management
    public static function initCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            if (function_exists('random_bytes')) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
            }
        }
    }

    public static function getCsrfToken() {
        self::initCsrfToken();
        return $_SESSION['csrf_token'];
    }

    public static function csrfField() {
        $token = self::getCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">' .
               '<input type="text" name="farm_security_code" value="" style="display:none !important;" tabindex="-1" autocomplete="off">';
    }

    public static function validateCsrfToken($token = null) {
        self::initCsrfToken();
        self::checkHoneypot();

        if ($token === null) {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '');
        }

        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }

    // XSS Escape Utility
    public static function escape($data) {
        if (is_array($data)) {
            return array_map(array('Security', 'escape'), $data);
        }
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }

    // Sanitize string input
    public static function sanitizeString($input) {
        $clean = strip_tags(trim((string)$input));
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $clean);
    }

    private static function readJsonFile($path) {
        if (!file_exists($path)) return array();
        $content = @file_get_contents($path);
        if (!$content) return array();
        $data = json_decode($content, true);
        return is_array($data) ? $data : array();
    }

    private static function writeJsonFile($path, $data) {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

// Auto-run security baseline
Security::init();
