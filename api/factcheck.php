<?php
/**
 * FASAL - Satya-Rakshak Live Fact-Check & Rumor Buster API
 */

header('Content-Type: application/json; charset=UTF-8');
define('FASAL_ROOT', dirname(__DIR__));

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/factcheck_engine.php';

$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true) ?: $_POST;

$action = isset($_GET['action']) ? $_GET['action'] : (isset($input['action']) ? $input['action'] : 'trending');

if ($action === 'trending') {
    $list = FactCheckEngine::getTrendingFactChecks();
    echo json_encode(array(
        'success' => true,
        'trending' => $list
    ));
    exit;
}

if ($action === 'verify') {
    $text = isset($input['query']) ? Security::sanitizeString($input['query']) : (isset($_GET['query']) ? Security::sanitizeString($_GET['query']) : '');
    if (empty($text)) {
        echo json_encode(array('success' => false, 'message' => 'Query is required'));
        exit;
    }

    $result = FactCheckEngine::verifyClaim($text);
    echo json_encode($result);
    exit;
}

echo json_encode(array('success' => false, 'error' => 'Invalid action'));
