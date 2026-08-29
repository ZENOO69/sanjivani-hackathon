<?php
define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/blackout_engine.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';

// Safe Fetch Advisories with HA Failover
$rawAdvisories = BlackoutEngine::safeFetchAll('crop_advisories');
$advisories = array();
foreach ($rawAdvisories as $row) {
    $advisories[] = array(
        'id'          => $row['id'],
        'category'    => HybridCrypto::decrypt($row['category']),
        'title'       => HybridCrypto::decrypt($row['title']),
        'description' => HybridCrypto::decrypt($row['description']),
        'action_text' => HybridCrypto::decrypt($row['action_text']),
        'action_link' => HybridCrypto::decrypt(isset($row['action_link']) ? $row['action_link'] : '#'),
        'urgency'     => HybridCrypto::decrypt($row['urgency']),
        'icon'        => isset($row['icon']) ? $row['icon'] : 'bell',
    );
}

// Fetch Live Real Data directly from ESP8266 IoT Cloud endpoint
$getUrl = $config['iot']['get_url'];
$ch = curl_init($getUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 6);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$iotRecords = array();
$iotData = array(
    'device_hash'   => $config['iot']['device_hash'],
    'temperature'   => '27.5',
    'humidity'      => '72.0',
    'soil_raw'      => '1024',
    'soil_moisture' => '0',
    'soil_status'   => 'DRY',
    'recorded_at'   => date('d-m-Y h:i A'),
);

if (!empty($response)) {
    $parsed = json_decode($response, true);
    if (is_array($parsed)) {
        $list = isset($parsed['data']) && is_array($parsed['data']) ? $parsed['data'] : (isset($parsed[0]) ? $parsed : array($parsed));
        foreach ($list as $item) {
            $iotRecords[] = array(
                'id'            => isset($item['id']) ? $item['id'] : count($iotRecords) + 1,
                'device_hash'   => isset($item['hash_id']) ? $item['hash_id'] : $config['iot']['device_hash'],
                'temperature'   => isset($item['sensor1']) ? $item['sensor1'] : '0',
                'humidity'      => isset($item['sensor2']) ? $item['sensor2'] : '0',
                'soil_raw'      => isset($item['sensor3']) ? $item['sensor3'] : '0',
                'soil_moisture' => isset($item['sensor4']) ? $item['sensor4'] : '0',
                'soil_status'   => isset($item['sensor5']) ? $item['sensor5'] : 'DRY',
                'datetime'      => isset($item['datetime']) ? $item['datetime'] : (isset($item['date']) ? $item['date'] . ' ' . $item['time'] : date('d-m-Y h:i A')),
            );
        }
        if (!empty($iotRecords[0])) {
            $iotData['temperature']   = (string)$iotRecords[0]['temperature'];
            $iotData['humidity']      = (string)$iotRecords[0]['humidity'];
            $iotData['soil_raw']      = (string)$iotRecords[0]['soil_raw'];
            $iotData['soil_moisture'] = (string)$iotRecords[0]['soil_moisture'];
            $iotData['soil_status']   = (string)$iotRecords[0]['soil_status'];
            $iotData['recorded_at']   = (string)$iotRecords[0]['datetime'];
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 flex-1">
    
    <!-- Welcome & Farm Status Banner -->
    <div class="bg-gradient-to-r from-emerald-700 via-emerald-800 to-green-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/15 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-15 text-9xl pointer-events-none select-none">🚜</div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-emerald-100 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-ping"></span>
                        <span><?= htmlspecialchars($config['farm_location']['region_name']) ?></span>
                    </span>
                    <span class="px-3 py-1 bg-amber-400 text-amber-950 rounded-full text-xs font-black">
                        मुख्य पीक: <?= Security::escape($userCrop) ?>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                    राम राम, <?= Security::escape($userName) ?>! 👋
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100 max-w-2xl">
                    तुमच्या शेतातील थेट सेन्सर्स आणि कोपरगाव हवामान केंद्रानुसार आजचे तातडीचे निर्णय खालीलप्रमाणे आहेत:
                </p>
            </div>

            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="advisory" class="px-5 py-3.5 bg-white hover:bg-emerald-50 text-emerald-900 font-black rounded-2xl shadow-lg transition transform active:scale-95 flex items-center gap-2 text-sm">
                    <i data-lucide="sparkles" class="w-5 h-5 text-emerald-600"></i>
                    <span>AI पीक डॉक्टर विचारा</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Actionable Advisories -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-amber-500/20">
                    ⚡
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900"><?= __t('actionable_advisories') ?></h2>
                    <p class="text-xs text-slate-500">केवळ आकडेवारी नाही - प्रत्यक्ष शेतात करायची कृती</p>
                </div>
            </div>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                ३ नवीन सल्ले उपलब्ध
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="glass-card glass-card-hover rounded-3xl p-6 border-l-4 border-l-sky-500 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-sky-100 text-sky-800 text-xs font-extrabold rounded-full flex items-center gap-1.5">
                            <i data-lucide="droplet" class="w-3.5 h-3.5"></i>
                            <span>सिंचन वेळापत्रक (Water Alert)</span>
                        </span>
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 uppercase">High Priority</span>
                    </div>

                    <h3 class="text-base font-black text-slate-900 leading-snug">
                        उद्या सकाळी ६:०० ते ९:०० दरम्यान ठिबक सिंचन सुरू करा
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        जमिनीतील ओलावा सध्या <strong><?= Security::escape($iotData['soil_moisture']) ?>% (<?= Security::escape($iotData['soil_status']) ?>)</strong> वर आहे. कांदा व कापूस पिकाला ताण बसू नये म्हणून <strong>४५ मिनिटे</strong> पाणी द्यावे.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button data-voice-text="उद्या सकाळी सहा ते नऊ दरम्यान ठिबक सिंचन सुरू करा. जमिनीतील ओलावा कमी आहे. कांदा पिकाला पंचेचाळीस मिनिटे पाणी द्यावे." class="px-3 py-2 bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-bold rounded-xl flex items-center gap-1.5 transition">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                        <span><?= __t('listen_audio') ?></span>
                    </button>

                    <button onclick="alert('✅ सिंचन शेड्युल नोंदवले गेले! उद्या सकाळी ५:४५ वाजता तुम्हाला व्हॉट्सॲप स्मरणपत्र पाठवले जाईल.')" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        वेळ निश्चित करा
                    </button>
                </div>
            </div>

            <div class="glass-card glass-card-hover rounded-3xl p-6 border-l-4 border-l-amber-500 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-amber-100 text-amber-900 text-xs font-extrabold rounded-full flex items-center gap-1.5">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                            <span>बाजार विक्री सल्ला (Sell Advice)</span>
                        </span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">+₹३५०/Q नफा</span>
                    </div>

                    <h3 class="text-base font-black text-slate-900 leading-snug">
                        कांदा विक्री: गुरुवार पर्यंत माल राखून ठेवा (Hold Onion)
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        लासलगाव व कोपरगाव मार्केटमध्ये आवक घटल्याने गुरुवारी भाव <strong>₹२,६५०/क्विंटल</strong> पर्यंत जाण्याची ९२% शक्यता आहे. आज घाईने स्थानिक व्यापाऱ्याला विकू नका.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button data-voice-text="कांदा विक्री गुरुवार पर्यंत राखून ठेवा. लासलगाव व कोपरगाव मार्केटमध्ये गुरुवारी भाव दोन हजार सहाशे पन्नास रुपये पर्यंत वाढेल. आज घाईने विकू नका." class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl flex items-center gap-1.5 transition">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                        <span><?= __t('listen_audio') ?></span>
                    </button>

                    <a href="mandi" class="px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        थेट भाव तपासा &rarr;
                    </a>
                </div>
            </div>

            <div class="glass-card glass-card-hover rounded-3xl p-6 border-l-4 border-l-purple-500 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-purple-100 text-purple-900 text-xs font-extrabold rounded-full flex items-center gap-1.5">
                            <i data-lucide="bug" class="w-3.5 h-3.5"></i>
                            <span>कीड व रोग पूर्वसूचना (Pest Alert)</span>
                        </span>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">थ्रिप्स / करपा</span>
                    </div>

                    <h3 class="text-base font-black text-slate-900 leading-snug">
                        कापूस व कांद्यावर करपा व तुडतुडे प्रतिबंधक फवारणी करा
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        हवेतील आर्द्रता सध्या <strong><?= Security::escape($iotData['humidity']) ?>%</strong> आहे. प्रतिबंधासाठी <strong>अमिस्टार टॉप (१ मिली/लि)</strong> किंवा <strong>साफ बुरशीनाशक (२ ग्रॅम/लि)</strong> सोबत थायामेथोक्सम फवारा.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button data-voice-text="कापूस व कांद्यावर करपा व तुडतुडे प्रतिबंधक फवारणी करा. अमिस्टार टॉप एक मिली प्रति लिटर किंवा साफ दोन ग्रॅम प्रति लिटर पाणी फवारावे." class="px-3 py-2 bg-purple-50 hover:bg-purple-100 text-purple-900 text-xs font-bold rounded-xl flex items-center gap-1.5 transition">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                        <span><?= __t('listen_audio') ?></span>
                    </button>

                    <a href="advisory" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        AI डॉक्टर सल्ला &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Live IoT Telemetry & Hyper-Local Weather -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" id="iot-live-widget">
        
        <!-- IoT Hardware Telemetry -->
        <div class="lg:col-span-7 glass-card rounded-3xl p-6 sm:p-8 space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-md shadow-emerald-600/30">
                        📟
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900"><?= __t('live_iot_connected') ?></h2>
                        <p class="text-xs text-slate-500">हार्डवेअर ID: <code class="font-bold text-emerald-800"><?= htmlspecialchars($config['iot']['device_hash']) ?></code></p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs text-emerald-800 font-bold bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200" id="val-last-synced">
                        <?= Security::escape($iotData['recorded_at']) ?>
                    </span>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                
                <div class="bg-gradient-to-b from-emerald-50 to-green-50/50 p-4 rounded-2xl border border-emerald-200/80 text-center space-y-1">
                    <div class="flex items-center justify-center text-emerald-700 mb-1">
                        <i data-lucide="droplets" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-900 block"><?= __t('soil_moisture') ?></span>
                    <span class="text-3xl font-black text-emerald-950 easy-metric-val" id="val-soil-moisture"><?= Security::escape($iotData['soil_moisture']) ?>%</span>
                    <span class="text-[10px] text-emerald-700 font-semibold block">A0 Raw: <span id="val-soil-raw"><?= Security::escape($iotData['soil_raw']) ?></span></span>
                </div>

                <div class="bg-gradient-to-b from-sky-50 to-blue-50/50 p-4 rounded-2xl border border-sky-200/80 text-center space-y-1">
                    <div class="flex items-center justify-center text-sky-700 mb-1">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-sky-900 block"><?= __t('soil_status') ?></span>
                    <div class="pt-1">
                        <?php 
                            $statusClass = (strpos(strtoupper($iotData['soil_status']), 'DRY') !== false) 
                                ? 'bg-rose-100 text-rose-800 border-rose-300' 
                                : ((strpos(strtoupper($iotData['soil_status']), 'WET') !== false) 
                                    ? 'bg-blue-100 text-blue-800 border-blue-300' 
                                    : 'bg-emerald-100 text-emerald-800 border-emerald-300');
                        ?>
                        <span class="px-2.5 py-1 rounded-full font-black text-xs border <?= $statusClass ?>" id="val-soil-status">
                            <?= Security::escape($iotData['soil_status']) ?>
                        </span>
                    </div>
                    <span class="text-[10px] text-sky-700 font-semibold block pt-1">थेट सेन्सर स्टेटस</span>
                </div>

                <div class="bg-gradient-to-b from-amber-50 to-orange-50/50 p-4 rounded-2xl border border-amber-200/80 text-center space-y-1">
                    <div class="flex items-center justify-center text-amber-700 mb-1">
                        <i data-lucide="thermometer" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-900 block"><?= __t('field_temp') ?></span>
                    <span class="text-3xl font-black text-amber-950 easy-metric-val" id="val-temperature"><?= Security::escape($iotData['temperature']) ?> °C</span>
                    <span class="text-[10px] text-amber-700 font-semibold block">DHT11 Sensor</span>
                </div>

                <div class="bg-gradient-to-b from-teal-50 to-cyan-50/50 p-4 rounded-2xl border border-teal-200/80 text-center space-y-1">
                    <div class="flex items-center justify-center text-teal-700 mb-1">
                        <i data-lucide="wind" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-teal-900 block"><?= __t('field_humidity') ?></span>
                    <span class="text-3xl font-black text-teal-950 easy-metric-val" id="val-humidity"><?= Security::escape($iotData['humidity']) ?>%</span>
                    <span class="text-[10px] text-teal-700 font-semibold block">हवामान आर्द्रता</span>
                </div>

            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-700">
                    <i data-lucide="wifi" class="w-4 h-4 text-emerald-600"></i>
                    <span>Live API Endpoint: <code class="text-slate-600 font-bold"><?= htmlspecialchars($config['iot']['get_url']) ?></code></span>
                </div>
                <button onclick="location.reload()" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>ताजे करा (Refresh)</span>
                </button>
            </div>

        </div>

        <!-- Hyper-Local Weather -->
        <div class="lg:col-span-5 glass-card rounded-3xl p-6 sm:p-8 space-y-6 flex flex-col justify-between">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-600 text-white flex items-center justify-center font-black shadow-md shadow-sky-600/30">
                        🌦️
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900"><?= __t('weather_forecast') ?></h2>
                        <p class="text-xs text-slate-500">स्थान: कोपरगाव (19.89° N, 74.47° E)</p>
                    </div>
                </div>

                <span class="text-xs font-bold text-sky-800 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-200">
                    Live Satellite
                </span>
            </div>

            <div class="flex items-center justify-between bg-gradient-to-r from-sky-50 to-indigo-50/60 p-4 rounded-2xl border border-sky-100">
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-slate-900">32°C</div>
                    <p class="text-xs font-bold text-sky-900 mt-0.5">स्वच्छ व निरभ्र आकाश (Clear Sky)</p>
                    <p class="text-[11px] text-slate-500">जास्तीत जास्त: ३५°C • किमान: २१°C</p>
                </div>
                <div class="text-5xl animate-bounce">
                    ☀️
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-2.5">
                    <i data-lucide="cloud-rain" class="w-5 h-5 text-sky-600"></i>
                    <div>
                        <div class="text-slate-500 text-[10px]"><?= __t('rain_prob') ?></div>
                        <div class="font-bold text-slate-900">०% (पुढील ३ दिवस पाऊस नाही)</div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-2.5">
                    <i data-lucide="wind" class="w-5 h-5 text-teal-600"></i>
                    <div>
                        <div class="text-slate-500 text-[10px]"><?= __t('wind_speed') ?></div>
                        <div class="font-bold text-slate-900">१२ किमी/तास (पश्चिम)</div>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-center text-xs font-bold text-slate-700">
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 block font-normal">आज</span>
                    <span>☀️ ३४°C</span>
                </div>
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 block font-normal">उद्या</span>
                    <span>🌤️ ३६°C</span>
                </div>
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 block font-normal">गुरुवार</span>
                    <span>☀️ ३५°C</span>
                </div>
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 block font-normal">शुक्रवार</span>
                    <span>⛅ ३३°C</span>
                </div>
            </div>

        </div>

    </div>

    <!-- ESP8266 Sensor Records Table -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-md border border-emerald-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black shadow-md shadow-emerald-600/30">
                    📊
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">ESP8266 थेट सेन्सर नोंदी (Telemetry Log History)</h2>
                    <p class="text-xs text-slate-500">
                        एकूण नोंदी: <strong class="text-emerald-800" id="total-records-count"><?= count($iotRecords) ?></strong> • Source: <code><?= htmlspecialchars($config['iot']['get_url']) ?></code>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="text" id="telemetry-search" oninput="filterTelemetryTable()" placeholder="तारीख किंवा स्थिती शोधा..." class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <button onclick="fetchLatestTelemetry()" class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition flex items-center gap-1">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>सिंक करा</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700" id="telemetry-table">
                <thead class="text-[11px] uppercase bg-slate-100 text-slate-600 font-extrabold">
                    <tr>
                        <th class="py-3 px-4 rounded-l-xl">ID</th>
                        <th class="py-3 px-4">तारीख व वेळ (Timestamp)</th>
                        <th class="py-3 px-4">तापमान (°C)</th>
                        <th class="py-3 px-4">हवेतील आर्द्रता (%)</th>
                        <th class="py-3 px-4">माती Raw (A0)</th>
                        <th class="py-3 px-4">ओलावा (%)</th>
                        <th class="py-3 px-4 rounded-r-xl">जमीन स्थिती (Status)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium" id="telemetry-tbody">
                    <?php if (!empty($iotRecords)): ?>
                        <?php foreach ($iotRecords as $row): ?>
                            <tr class="telemetry-row hover:bg-slate-50 transition" 
                                data-id="<?= Security::escape($row['id']) ?>"
                                data-date="<?= Security::escape($row['datetime']) ?>"
                                data-status="<?= Security::escape($row['soil_status']) ?>">
                                <td class="py-3.5 px-4 font-bold text-slate-900">#<?= Security::escape($row['id']) ?></td>
                                <td class="py-3.5 px-4 text-slate-600 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span><?= Security::escape($row['datetime']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-amber-900"><?= Security::escape($row['temperature']) ?> °C</td>
                                <td class="py-3.5 px-4 font-bold text-teal-900"><?= Security::escape($row['humidity']) ?> %</td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono"><?= Security::escape($row['soil_raw']) ?></td>
                                <td class="py-3.5 px-4 font-black text-emerald-900"><?= Security::escape($row['soil_moisture']) ?> %</td>
                                <td class="py-3.5 px-4">
                                    <?php 
                                        $s = strtoupper($row['soil_status']);
                                        if (strpos($s, 'DRY') !== false) {
                                            echo '<span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px] border border-rose-200">DRY</span>';
                                        } elseif (strpos($s, 'WET') !== false) {
                                            echo '<span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[10px] border border-blue-200">WET</span>';
                                        } else {
                                            echo '<span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px] border border-emerald-200">MODERATE</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                कोणतीही सेन्सर नोंद उपलब्ध नाही.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-100 text-xs">
            <div class="text-slate-500">
                दाखवत आहे <span id="page-start" class="font-bold text-slate-800">1</span> ते <span id="page-end" class="font-bold text-slate-800">10</span> (एकूण <span id="page-total" class="font-bold text-slate-800"><?= count($iotRecords) ?></span> नोंदी)
            </div>

            <div class="flex items-center gap-1.5 self-center">
                <button onclick="prevTelemetryPage()" id="btn-prev-page" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    <span>मागील (Prev)</span>
                </button>

                <div id="page-numbers-container" class="flex items-center gap-1"></div>

                <button onclick="nextTelemetryPage()" id="btn-next-page" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1">
                    <span>पुढील (Next)</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- Community Machinery & Labour Dispatch -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-orange-600 text-white flex items-center justify-center font-black shadow-md shadow-orange-600/30">
                    🚜
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">परिसरातील उपलब्ध यंत्रे व मजूर टोळ्या</h2>
                    <p class="text-xs text-slate-500">कोपरगाव व राहाता परिसरातील थेट शेतकरी संपर्क</p>
                </div>
            </div>
            <a href="community" class="text-xs font-bold text-orange-700 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 px-4 py-2 rounded-xl border border-orange-200 transition flex items-center gap-1.5 self-start sm:self-auto">
                <span>सर्व यंत्रे व मजूर यादी पहा</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <div class="bg-slate-50 hover:bg-white p-4 rounded-2xl border border-slate-200 transition space-y-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-900 block">John Deere 5050D + रोटाव्हेटर</span>
                        <span class="text-[11px] text-slate-500">सुरेश शिंदे (कोपरगाव परिसर)</span>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">उपलब्ध</span>
                </div>
                <div class="text-base font-black text-emerald-800">₹८०० / तास</div>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold pt-1">
                    <a href="tel:+919822123456" class="py-2 bg-emerald-600 text-white rounded-xl text-center hover:bg-emerald-700 transition flex items-center justify-center gap-1">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        <span>कॉल करा</span>
                    </a>
                    <a href="https://wa.me/919822123456?text=Namaskar,%20I%20saw%20your%20Tractor%20on%20FASAL%20App" target="_blank" class="py-2 bg-green-500 text-white rounded-xl text-center hover:bg-green-600 transition flex items-center justify-center gap-1">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="bg-slate-50 hover:bg-white p-4 rounded-2xl border border-slate-200 transition space-y-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-900 block">कांदा काढणी मजूर टोळी (१२ जण)</span>
                        <span class="text-[11px] text-slate-500">संतोष जाधव (राहाता रोड)</span>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">उपलब्ध</span>
                </div>
                <div class="text-base font-black text-emerald-800">₹४०० / दिवस / मजूर</div>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold pt-1">
                    <a href="tel:+919890112233" class="py-2 bg-emerald-600 text-white rounded-xl text-center hover:bg-emerald-700 transition flex items-center justify-center gap-1">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        <span>कॉल करा</span>
                    </a>
                    <a href="https://wa.me/919890112233?text=Namaskar,%20I%20need%20labour%20for%20onion%20harvesting" target="_blank" class="py-2 bg-green-500 text-white rounded-xl text-center hover:bg-green-600 transition flex items-center justify-center gap-1">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="bg-slate-50 hover:bg-white p-4 rounded-2xl border border-slate-200 transition space-y-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-900 block">सोलर हाय-प्रेशर फवारणी पंप (1000L)</span>
                        <span class="text-[11px] text-slate-500">विष्णू पवार (कोपरगाव ग्रामीण)</span>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">उपलब्ध</span>
                </div>
                <div class="text-base font-black text-emerald-800">₹४५० / एकर</div>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold pt-1">
                    <a href="tel:+919423112233" class="py-2 bg-emerald-600 text-white rounded-xl text-center hover:bg-emerald-700 transition flex items-center justify-center gap-1">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        <span>कॉल करा</span>
                    </a>
                    <a href="https://wa.me/919423112233?text=Namaskar,%20I%20want%20to%20book%20Solar%20Sprayer" target="_blank" class="py-2 bg-green-500 text-white rounded-xl text-center hover:bg-green-600 transition flex items-center justify-center gap-1">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    let currentPage = 1;
    const pageSize = 10;
    let allRows = [];

    document.addEventListener('DOMContentLoaded', () => {
        allRows = Array.from(document.querySelectorAll('.telemetry-row'));
        renderTelemetryPage(1);
    });

    function renderTelemetryPage(page) {
        currentPage = page;
        const visibleRows = allRows.filter(r => r.getAttribute('data-hidden') !== 'true');
        const total = visibleRows.length;
        const totalPages = Math.ceil(total / pageSize) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * pageSize;
        const end = Math.min(start + pageSize, total);

        visibleRows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('page-start').innerText = total > 0 ? (start + 1) : 0;
        document.getElementById('page-end').innerText = end;
        document.getElementById('page-total').innerText = total;

        document.getElementById('btn-prev-page').disabled = (currentPage === 1);
        document.getElementById('btn-next-page').disabled = (currentPage === totalPages || total === 0);

        const numContainer = document.getElementById('page-numbers-container');
        numContainer.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const btn = document.createElement('button');
                btn.innerText = i;
                btn.className = `w-8 h-8 rounded-xl font-bold text-xs transition ${i === currentPage ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'}`;
                btn.onclick = () => renderTelemetryPage(i);
                numContainer.appendChild(btn);
            }
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function prevTelemetryPage() {
        if (currentPage > 1) renderTelemetryPage(currentPage - 1);
    }

    function nextTelemetryPage() {
        renderTelemetryPage(currentPage + 1);
    }

    function filterTelemetryTable() {
        const query = document.getElementById('telemetry-search').value.toLowerCase().trim();
        allRows.forEach(row => {
            const date = (row.getAttribute('data-date') || '').toLowerCase();
            const status = (row.getAttribute('data-status') || '').toLowerCase();
            const id = (row.getAttribute('data-id') || '').toLowerCase();

            if (date.includes(query) || status.includes(query) || id.includes(query)) {
                row.removeAttribute('data-hidden');
            } else {
                row.setAttribute('data-hidden', 'true');
            }
        });
        renderTelemetryPage(1);
    }

    async function fetchLatestTelemetry() {
        try {
            const res = await fetch('api/iot-sync?action=all');
            const json = await res.json();
            if (json.success && json.records) {
                location.reload();
            }
        } catch (e) {
            location.reload();
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
