<?php
if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

$config = isset($GLOBALS['FASAL_CONFIG']) ? $GLOBALS['FASAL_CONFIG'] : require __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/backup.php';
require_once __DIR__ . '/blackout_engine.php';
require_once __DIR__ . '/translations.php';

$pdo = Database::getConnection();
$tickerItems = array();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM `mandi_prices` ORDER BY id ASC LIMIT 8");
        while ($row = $stmt->fetch()) {
            $tickerItems[] = array(
                'name'     => HybridCrypto::decrypt($row['commodity_name']),
                'market'   => HybridCrypto::decrypt($row['market_name']),
                'price'    => HybridCrypto::decrypt($row['modal_price']),
                'trend'    => HybridCrypto::decrypt($row['price_trend']),
                'percent'  => HybridCrypto::decrypt($row['trend_percentage']),
            );
        }
    } catch (Exception $e) {
        $tickerItems = array();
    }
}

$isLoggedIn = !empty($_SESSION['user_id']);
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'शेतकरी मित्र (Farmer)';
$userCrop = isset($_SESSION['primary_crop']) ? $_SESSION['primary_crop'] : 'कांदा (Onion)';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?= I18n::getLang() ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#064E3B">
    <meta name="csrf-token" content="<?= Security::getCsrfToken() ?>">
    <title><?= htmlspecialchars($config['app']['name']) ?> - <?= htmlspecialchars($config['app']['tagline']) ?></title>
    <meta name="description" content="FASAL: Unified Farmer Decision-Intelligence & Advisory Platform. Live Weather, IoT Soil Telemetry, Mandi Rates, and Gemini AI Crop Doctor.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">

    <!-- TOP LIVE APMC MANDI TICKER MARQUEE & BLACKOUT MONITOR -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-950 text-white text-xs py-2 px-4 border-b border-emerald-800/40 relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 overflow-hidden flex-1">
                <div class="flex items-center gap-1.5 bg-emerald-700/80 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider flex-shrink-0 text-emerald-100 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span><?= __t('mandi_live_rates') ?></span>
                </div>

                <div class="marquee-container flex-1 overflow-hidden">
                    <div class="marquee-content gap-8 text-xs font-medium">
                        <?php if (!empty($tickerItems)): ?>
                            <?php foreach (array_merge($tickerItems, $tickerItems) as $item): ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-300 font-bold"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="text-slate-300">(<?= htmlspecialchars($item['market']) ?>):</span>
                                    <span class="font-extrabold text-amber-300">₹<?= htmlspecialchars($item['price']) ?>/Q</span>
                                    <?php if ($item['trend'] === 'up'): ?>
                                        <span class="text-emerald-400 font-bold text-[11px] flex items-center">▲ <?= htmlspecialchars($item['percent']) ?></span>
                                    <?php elseif ($item['trend'] === 'down'): ?>
                                        <span class="text-rose-400 font-bold text-[11px] flex items-center">▼ <?= htmlspecialchars($item['percent']) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 font-bold text-[11px]">▬ 0.0%</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="flex items-center gap-6">
                                <span>🌱 <strong>कापूस (Cotton - Kopargaon):</strong> ₹7,650/Q <span class="text-emerald-400">▲ +4.2%</span></span>
                                <span>🧅 <strong>कांदा (Onion - Lasalgaon):</strong> ₹2,400/Q <span class="text-emerald-400">▲ +6.8%</span></span>
                                <span>🍊 <strong>संत्रा (Orange - Nagpur):</strong> ₹4,800/Q <span class="text-emerald-400">▲ +2.5%</span></span>
                                <span>🌾 <strong>सोयाबीन (Soybean - Latur):</strong> ₹4,750/Q <span class="text-rose-400">▼ -1.2%</span></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Truth Radar & Blackout Quick Triggers -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="factcheck" class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-600/90 hover:bg-emerald-500 text-white font-black text-[10px] tracking-wide uppercase transition shadow-sm border border-emerald-400/40 active:scale-95" title="The Bad Reading: AI Truth Verification & Rumor Buster">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-ping"></span>
                    <span>🛡️ सत्य-रक्षक (Truth Radar)</span>
                </a>
                <button onclick="openBlackoutModal()" class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-600/90 hover:bg-rose-500 text-white font-black text-[10px] tracking-wide uppercase transition shadow-sm border border-rose-400/40 active:scale-95" title="The Blackout: Live Data Wipe & Self-Healing Resilience Test">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    <span>⚡ The Blackout (Live Test)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN APP NAVBAR -->
    <header class="bg-white/95 backdrop-blur-md border-b border-emerald-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18 gap-2 lg:gap-4">
                
                <!-- Logo -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="index" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-emerald-600 via-green-600 to-amber-400 flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-600/20 group-hover:scale-105 transition transform">
                            🌱
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xl sm:text-2xl font-black tracking-tight text-emerald-950"><?= htmlspecialchars($config['app']['name']) ?></span>
                                <span class="hidden xl:inline-block px-1.5 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-[9px] rounded-full uppercase">Kopargaon Live</span>
                            </div>
                            <p class="text-[10px] text-emerald-700 font-medium hidden lg:block truncate max-w-[200px]"><?= htmlspecialchars($config['app']['tagline']) ?></p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Nav Links (Clean Single-Line Aligned) -->
                <nav class="hidden md:flex items-center gap-1 text-xs lg:text-sm font-semibold text-slate-700">
                    <a href="dashboard" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center <?= in_array($currentPage, array('dashboard', '')) ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'hover:bg-slate-100 hover:text-emerald-700' ?>">
                        <?= __t('nav_dashboard') ?>
                    </a>
                    <a href="advisory" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center <?= $currentPage === 'advisory' ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'hover:bg-slate-100 hover:text-emerald-700' ?>">
                        <?= __t('nav_advisory') ?>
                    </a>
                    <a href="mandi" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center <?= $currentPage === 'mandi' ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'hover:bg-slate-100 hover:text-emerald-700' ?>">
                        <?= __t('nav_mandi') ?>
                    </a>
                    <a href="community" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center <?= $currentPage === 'community' ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'hover:bg-slate-100 hover:text-emerald-700' ?>">
                        <?= __t('nav_community') ?>
                    </a>
                    <a href="schemes" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center <?= $currentPage === 'schemes' ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'hover:bg-slate-100 hover:text-emerald-700' ?>">
                        <?= __t('nav_schemes') ?>
                    </a>
                    <a href="factcheck" class="px-2.5 lg:px-3 py-1.5 rounded-lg transition whitespace-nowrap h-9 inline-flex items-center gap-1 <?= $currentPage === 'factcheck' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-emerald-50/80 text-emerald-800 hover:bg-emerald-100 font-semibold border border-emerald-200/60' ?>">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-500"></i>
                        <span><?= __t('nav_factcheck') ?></span>
                    </a>
                </nav>

                <!-- Actions: Easy Mode, Language & Profile -->
                <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                    <button onclick="toggleEasyMode()" class="easy-mode-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-200 text-xs font-semibold bg-amber-50 text-amber-900 hover:bg-amber-100 transition shadow-sm h-8 sm:h-9 whitespace-nowrap" title="सुलभ शेतकरी मोड / Easy Mode">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-amber-700"></i>
                        <span class="hidden sm:inline"><?= __t('easy_mode') ?></span>
                    </button>

                    <div class="inline-flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200 text-xs font-bold h-8 sm:h-9">
                        <a href="?lang=mr" class="px-2 py-0.5 rounded-md transition <?= I18n::getLang() === 'mr' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-emerald-700' ?>">मराठी</a>
                        <a href="?lang=hi" class="px-2 py-0.5 rounded-md transition <?= I18n::getLang() === 'hi' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-emerald-700' ?>">हिंदी</a>
                        <a href="?lang=en" class="px-2 py-0.5 rounded-md transition <?= I18n::getLang() === 'en' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-emerald-700' ?>">EN</a>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <div class="relative group">
                            <a href="profile" class="inline-flex items-center gap-1.5 pl-1 pr-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition h-8 sm:h-9">
                                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-emerald-600 text-white font-black text-xs flex items-center justify-center shadow-sm">
                                    <?= mb_substr($userName, 0, 1, 'UTF-8') ?>
                                </div>
                                <div class="hidden lg:block text-left text-xs">
                                    <div class="font-bold text-emerald-950 truncate max-w-[90px] leading-tight"><?= htmlspecialchars($userName) ?></div>
                                    <div class="text-[9px] text-emerald-700 leading-none truncate max-w-[90px]"><?= htmlspecialchars($userCrop) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="auth?action=login_view" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm transition inline-flex items-center gap-1 h-8 sm:h-9 whitespace-nowrap">
                            <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                            <span><?= __t('nav_login') ?></span>
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </header>

    <?php require_once __DIR__ . '/nav.php'; ?>
