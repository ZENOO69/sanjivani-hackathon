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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_machinery') {
    if (!Security::validateCsrfToken()) {
        $msg = 'सुरक्षा पडताळणी अयशस्वी (Invalid CSRF Token)';
    } else {
        $eqName   = Security::sanitizeString(isset($_POST['equipment_name']) ? $_POST['equipment_name'] : '');
        $ownName  = Security::sanitizeString(isset($_POST['owner_name']) ? $_POST['owner_name'] : '');
        $ownPhone = Security::sanitizeString(isset($_POST['owner_phone']) ? $_POST['owner_phone'] : '');
        $loc      = Security::sanitizeString(isset($_POST['location']) ? $_POST['location'] : '');
        $rate     = Security::sanitizeString(isset($_POST['hourly_rate']) ? $_POST['hourly_rate'] : '');

        if (!empty($eqName) && !empty($ownName) && !empty($ownPhone)) {
            $recordData = array(
                'equipment_name' => HybridCrypto::encrypt($eqName),
                'owner_name'     => HybridCrypto::encrypt($ownName),
                'owner_phone'    => HybridCrypto::encrypt($ownPhone),
                'location'       => HybridCrypto::encrypt($loc),
                'hourly_rate'    => HybridCrypto::encrypt($rate),
                'status'         => HybridCrypto::encrypt('Available'),
            );

            // Record to Write-Ahead Journal first (WAL resilience)
            BlackoutEngine::recordMutation('machinery_listings', 'INSERT', $recordData);

            if ($pdo) {
                try {
                    $ins = $pdo->prepare("
                        INSERT INTO `machinery_listings` (`equipment_name`, `owner_name`, `owner_phone`, `location`, `hourly_rate`, `status`)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $ins->execute(array(
                        $recordData['equipment_name'],
                        $recordData['owner_name'],
                        $recordData['owner_phone'],
                        $recordData['location'],
                        $recordData['hourly_rate'],
                        $recordData['status'],
                    ));
                } catch (Exception $e) {}
            }
            $msg = 'तुमचे अवजार यशस्वीरीत्या नोंदवले गेले आहे!';
        }
    }
}

// Fetch Machinery Listings with HA failover
$rawMachinery = BlackoutEngine::safeFetchAll('machinery_listings');
$machineryList = array();
foreach ($rawMachinery as $r) {
    $machineryList[] = array(
        'id'       => $r['id'],
        'name'     => HybridCrypto::decrypt($r['equipment_name']),
        'owner'    => HybridCrypto::decrypt($r['owner_name']),
        'phone'    => HybridCrypto::decrypt($r['owner_phone']),
        'location' => HybridCrypto::decrypt($r['location']),
        'rate'     => HybridCrypto::decrypt($r['hourly_rate']),
        'status'   => HybridCrypto::decrypt($r['status']),
    );
}

// Fetch Labour Listings with HA failover
$rawLabour = BlackoutEngine::safeFetchAll('labour_listings');
$labourList = array();
foreach ($rawLabour as $r) {
    $labourList[] = array(
        'id'        => $r['id'],
        'group'     => HybridCrypto::decrypt($r['group_name']),
        'leader'    => HybridCrypto::decrypt($r['leader_name']),
        'phone'     => HybridCrypto::decrypt($r['leader_phone']),
        'count'     => HybridCrypto::decrypt($r['worker_count']),
        'specialty' => HybridCrypto::decrypt($r['specialty']),
        'wage'      => HybridCrypto::decrypt($r['daily_wage']),
        'location'  => HybridCrypto::decrypt($r['location']),
    );
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 flex-1">
    
    <div class="bg-gradient-to-r from-orange-600 via-amber-700 to-emerald-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-orange-950/15 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 opacity-20 text-9xl select-none">🚜</div>
        
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-extrabold text-orange-100">
                <span class="w-2 h-2 rounded-full bg-orange-300 animate-ping"></span>
                <span>शेतकरी ते शेतकरी थेट सेवा (Peer-to-Peer Farm Equipment Sharing)</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                <?= __t('nav_community') ?>
            </h1>
            <p class="text-xs sm:text-sm text-orange-100 leading-relaxed">
                काढणी, पेरणी, नांगरणीसाठी ट्रॅक्टर, रोटाव्हेटर, हार्वेस्टर आणि अनुभवी मजूर टोळ्या थेट बुक करा. दलालांशिवाय १-क्लिक कॉल व व्हॉट्सॲप!
            </p>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-800 font-bold text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span><?= Security::escape($msg) ?></span>
        </div>
    <?php endif; ?>

    <!-- Machinery Section -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900"><?= __t('rent_machinery') ?></h2>
                <p class="text-xs text-slate-500">कोपरगाव, राहाता व संगमनेर परिसरातील अवजारे</p>
            </div>
            <button onclick="document.getElementById('add-machine-modal').classList.remove('hidden')" class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 self-start sm:self-auto">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>तुमचे अवजार नोंदवा (+ Add Equipment)</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($machineryList as $m): ?>
                <div class="glass-card glass-card-hover rounded-3xl p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-start justify-between">
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-orange-600"></i>
                                <span><?= Security::escape($m['location']) ?></span>
                            </span>
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full uppercase">
                                <?= Security::escape($m['status']) ?>
                            </span>
                        </div>
                        <h3 class="text-base font-black text-slate-900 leading-snug"><?= Security::escape($m['name']) ?></h3>
                        <p class="text-xs text-slate-600">मालक: <strong><?= Security::escape($m['owner']) ?></strong></p>
                        <div class="text-lg font-black text-emerald-800 pt-1"><?= Security::escape($m['rate']) ?></div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs font-bold">
                        <a href="tel:<?= Security::escape($m['phone']) ?>" class="py-2.5 bg-emerald-600 text-white rounded-xl text-center hover:bg-emerald-700 transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>कॉल करा</span>
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $m['phone']) ?>?text=Namaskar,%20I%20saw%20your%20<?= urlencode($m['name']) ?>%20on%20FASAL%20App" target="_blank" class="py-2.5 bg-green-500 text-white rounded-xl text-center hover:bg-green-600 transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Labour Pool Section -->
    <div class="space-y-4 pt-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900"><?= __t('book_labour') ?></h2>
            <p class="text-xs text-slate-500">कांदा, कापूस, ऊस काढणी व ठिबक जोडणीसाठी कुशल कामगार टोळ्या</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($labourList as $l): ?>
                <div class="glass-card glass-card-hover rounded-3xl p-6 space-y-4 flex flex-col justify-between border-l-4 border-l-orange-500">
                    <div class="space-y-2">
                        <div class="flex items-start justify-between">
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-orange-600"></i>
                                <span><?= Security::escape($l['location']) ?></span>
                            </span>
                            <span class="px-2.5 py-0.5 bg-orange-100 text-orange-800 text-[10px] font-black rounded-full">
                                <?= Security::escape($l['count']) ?>
                            </span>
                        </div>
                        <h3 class="text-base font-black text-slate-900 leading-snug"><?= Security::escape($l['group']) ?></h3>
                        <p class="text-xs text-slate-600">प्रमुख: <strong><?= Security::escape($l['leader']) ?></strong></p>
                        <p class="text-xs text-amber-900 bg-amber-50 p-2 rounded-xl border border-amber-200/60 font-semibold">काम: <?= Security::escape($l['specialty']) ?></p>
                        <div class="text-base font-black text-emerald-800 pt-1"><?= Security::escape($l['wage']) ?></div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs font-bold">
                        <a href="tel:<?= Security::escape($l['phone']) ?>" class="py-2.5 bg-emerald-600 text-white rounded-xl text-center hover:bg-emerald-700 transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>कॉल करा</span>
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $l['phone']) ?>?text=Namaskar,%20I%20want%20to%20book%20your%20labour%20group%20for%20farming" target="_blank" class="py-2.5 bg-green-500 text-white rounded-xl text-center hover:bg-green-600 transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- Modal: Add Machinery Listing -->
<div id="add-machine-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl p-6 sm:p-8 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-900">तुमचे कृषी अवजार भाड्याने द्या</h3>
            <button onclick="document.getElementById('add-machine-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form action="community" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_machinery">
            <?= Security::csrfField() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">अवजाराचे नाव (उदा. महिंद्रा ५७५ ट्रॅक्टर + कल्टीव्हेटर)</label>
                <input type="text" name="equipment_name" required placeholder="ट्रॅक्टर / रोटाव्हेटर / हार्वेस्टर" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">मालकाचे नाव</label>
                    <input type="text" name="owner_name" required placeholder="उदा. अमोल काळे" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">मोबाईल नंबर</label>
                    <input type="tel" name="owner_phone" required placeholder="+91 98220 00000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">स्थान / गाव</label>
                    <input type="text" name="location" required placeholder="कोपरगाव / राहाता" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">भाडे दर</label>
                    <input type="text" name="hourly_rate" required placeholder="₹८०० / तास किंवा ₹२,००० / एकर" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-black rounded-2xl shadow-md transition transform active:scale-95">
                अवजार यादीत जोडा (Save Listing)
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
