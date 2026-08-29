<?php
define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/blackout_engine.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';

$pdo = Database::getConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!Security::validateCsrfToken()) {
        $msg = 'सुरक्षा पडताळणी अयशस्वी (Invalid CSRF Token)';
    } else {
        $name     = Security::sanitizeString(isset($_POST['full_name']) ? $_POST['full_name'] : '');
        $crop     = Security::sanitizeString(isset($_POST['primary_crop']) ? $_POST['primary_crop'] : '');
        $location = Security::sanitizeString(isset($_POST['farm_location']) ? $_POST['farm_location'] : '');
        $lang     = Security::sanitizeString(isset($_POST['preferred_lang']) ? $_POST['preferred_lang'] : 'mr');

        $_SESSION['user_name'] = $name;
        $_SESSION['primary_crop'] = $crop;
        $_SESSION['farm_location'] = $location;
        $_SESSION['lang'] = $lang;

        if (!empty($_SESSION['user_id'])) {
            $updateData = array(
                'id' => $_SESSION['user_id'],
                'full_name' => HybridCrypto::encrypt($name),
                'primary_crop' => HybridCrypto::encrypt($crop),
                'farm_location' => HybridCrypto::encrypt($location),
                'preferred_lang' => $lang,
            );
            BlackoutEngine::recordMutation('users', 'UPDATE', $updateData);

            if ($pdo) {
                try {
                    $upd = $pdo->prepare("
                        UPDATE `users` 
                        SET `full_name` = ?, `primary_crop` = ?, `farm_location` = ?, `preferred_lang` = ?
                        WHERE `id` = ?
                    ");
                    $upd->execute(array(
                        $updateData['full_name'],
                        $updateData['primary_crop'],
                        $updateData['farm_location'],
                        $lang,
                        $_SESSION['user_id']
                    ));
                } catch (Exception $e) {}
            }
        }
        $msg = 'प्रोफाइल व शेती माहिती यशस्वीरीत्या जतन केली!';
    }
}

$currName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'शेतकरी मित्र';
$currEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'farmer@fasal-agri.in';
$currCrop = isset($_SESSION['primary_crop']) ? $_SESSION['primary_crop'] : 'कांदा (Onion)';
$currLoc = isset($_SESSION['farm_location']) ? $_SESSION['farm_location'] : 'Kopargaon, Ahmednagar';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-8 flex-1">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900"><?= __t('nav_profile') ?></h1>
            <p class="text-xs sm:text-sm text-slate-500">तुमच्या शेताचे स्थान, पिके आणि सुरक्षा सेटिंग्ज</p>
        </div>

        <a href="auth?action=logout" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-sm">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span><?= __t('nav_logout') ?></span>
        </a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span><?= Security::escape($msg) ?></span>
        </div>
    <?php endif; ?>

    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
        
        <form action="profile" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="update_profile">
            <?= Security::csrfField() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">शेतकऱ्याचे संपूर्ण नाव</label>
                    <input type="text" name="full_name" value="<?= Security::escape($currName) ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">ईमेल पत्ता</label>
                    <input type="email" value="<?= Security::escape($currEmail) ?>" readonly class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">मुख्य पीक (Primary Crop)</label>
                    <select name="primary_crop" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="कांदा (Onion)" <?= $currCrop === 'कांदा (Onion)' ? 'selected' : '' ?>>कांदा (Onion)</option>
                        <option value="कापूस (Cotton)" <?= $currCrop === 'कापूस (Cotton)' ? 'selected' : '' ?>>कापूस (Cotton)</option>
                        <option value="ऊस (Sugarcane)" <?= $currCrop === 'ऊस (Sugarcane)' ? 'selected' : '' ?>>ऊस (Sugarcane)</option>
                        <option value="सोयाबीन (Soybean)" <?= $currCrop === 'सोयाबीन (Soybean)' ? 'selected' : '' ?>>सोयाबीन (Soybean)</option>
                        <option value="डाळिंब (Pomegranate)" <?= $currCrop === 'डाळिंब (Pomegranate)' ? 'selected' : '' ?>>डाळिंब (Pomegranate)</option>
                        <option value="संत्रा (Orange)" <?= $currCrop === 'संत्रा (Orange)' ? 'selected' : '' ?>>संत्रा (Orange)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">प्राथमिक भाषा (Language)</label>
                    <select name="preferred_lang" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="mr" <?= I18n::getLang() === 'mr' ? 'selected' : '' ?>>मराठी (Marathi)</option>
                        <option value="hi" <?= I18n::getLang() === 'hi' ? 'selected' : '' ?>>हिंदी (Hindi)</option>
                        <option value="en" <?= I18n::getLang() === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">शेताचे स्थान / तालुका (GPS Region)</label>
                <input type="text" name="farm_location" value="<?= Security::escape($currLoc) ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-600/20 transition transform active:scale-95 text-sm">
                माहिती जतन करा (Save Settings)
            </button>
        </form>

    </div>

    <!-- Security Badge Card -->
    <div class="p-6 rounded-3xl bg-slate-900 text-white space-y-4 shadow-xl">
        <div class="flex items-center gap-2 text-emerald-400 font-extrabold text-xs uppercase tracking-wider">
            <i data-lucide="shield-check" class="w-5 h-5"></i>
            <span>सुरक्षा व डेटा कूटबद्धीकरण (Security & Threat Protections)</span>
        </div>
        <p class="text-xs text-slate-300 leading-relaxed">
            तुमचा वैयक्तिक डेटा <strong>AES-256-CBC + HMAC-SHA256</strong> मिलिटरी-ग्रेड अल्गोरिदमने कूटबद्ध केलेला आहे. सिस्टीम DoS/DDoS रेट लिमिटिंग, SQLi इम्युनायझेशन, आणि ऑटोमॅटिक डेली बॅकअप स्नॅपशॉटने संरक्षित आहे.
        </p>
        <div class="flex flex-wrap gap-2 text-[10px] font-bold text-slate-400">
            <span class="px-2.5 py-1 bg-slate-800 rounded-lg border border-slate-700">AES-256 Encrypted</span>
            <span class="px-2.5 py-1 bg-slate-800 rounded-lg border border-slate-700">DoS / DDoS Mitigation</span>
            <span class="px-2.5 py-1 bg-slate-800 rounded-lg border border-slate-700">Automatic Daily Backup</span>
            <span class="px-2.5 py-1 bg-slate-800 rounded-lg border border-slate-700">The Blackout Self-Healing</span>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
