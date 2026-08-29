<?php
/**
 * ====================================================================
 * FASAL - Maharashtra APMC Mandi Rates & Profit Maximizer
 * ====================================================================
 */

define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';

$pdo = Database::getConnection();
$mandiRows = array();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM `mandi_prices` ORDER BY id ASC");
        while ($row = $stmt->fetch()) {
            $mandiRows[] = array(
                'id'       => $row['id'],
                'code'     => $row['commodity_code'],
                'name'     => HybridCrypto::decrypt($row['commodity_name']),
                'market'   => HybridCrypto::decrypt($row['market_name']),
                'min'      => HybridCrypto::decrypt($row['min_price']),
                'max'      => HybridCrypto::decrypt($row['max_price']),
                'modal'    => HybridCrypto::decrypt($row['modal_price']),
                'trend'    => HybridCrypto::decrypt($row['price_trend']),
                'percent'  => HybridCrypto::decrypt($row['trend_percentage']),
                'updated'  => date('d M, h:i A', strtotime(isset($row['updated_at']) ? $row['updated_at'] : 'now')),
            );
        }
    } catch (Exception $e) {
        // Keep moving
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 flex-1">
    
    <!-- Top Hero Banner -->
    <div class="bg-gradient-to-r from-amber-600 via-emerald-700 to-green-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/15 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 opacity-20 text-9xl select-none">📈</div>
        
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-extrabold text-amber-100">
                <span class="w-2 h-2 rounded-full bg-amber-300 animate-ping"></span>
                <span>थेट APMC बाजार भाव व आगामी नफा अंदाज (Decision Intelligence)</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                <?= __t('mandi_live_rates') ?>
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100 leading-relaxed">
                कोपरगाव, लासलगाव, राहाता, श्रीरामपूर, अकोला व नागपूर बाजार समित्यांमधील ताजे दर. वाहतूक खर्च वजा जाता कोणत्या बाजारात सर्वाधिक नफा मिळेल हे खालील कॅल्क्युलेटरने तपासा.
            </p>
        </div>
    </div>

    <!-- 1. MANDI PROFIT MAXIMIZER CALCULATOR (Actionable Decision Maker) -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border-2 border-amber-300 shadow-lg">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-md shadow-amber-500/30">
                    💰
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900">बाजार नफा कॅल्क्युलेटर (Mandi Profit Maximizer)</h2>
                    <p class="text-xs text-slate-500">वाहतूक खर्च वजा करून सर्वात फायदेशीर बाजारपेठ निवडा</p>
                </div>
            </div>
            <span class="text-xs font-bold text-amber-900 bg-amber-100 px-3 py-1 rounded-full">
                स्मार्ट विक्री निर्णय
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">विक्रीचे पीक (Commodity)</label>
                <select id="calc-commodity" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="onion">कांदा (Onion) - क्विंटल</option>
                    <option value="cotton">कापूस (Cotton) - क्विंटल</option>
                    <option value="orange">संत्रा (Orange) - क्विंटल</option>
                    <option value="soybean">सोयाबीन (Soybean) - क्विंटल</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">एकूण माल (Quantity in Quintals)</label>
                <input type="number" id="calc-quantity" value="25" min="1" max="1000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="flex items-end">
                <button onclick="calculateMandiProfit()" class="w-full py-3.5 bg-gradient-to-r from-amber-600 to-emerald-600 hover:from-amber-700 hover:to-emerald-700 text-white font-black rounded-xl shadow-md transition transform active:scale-95 text-sm flex items-center justify-center gap-2">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                    <span>सर्वोत्तम बाजारपेठ शोधा</span>
                </button>
            </div>
        </div>

        <!-- Calculated Result Output Card -->
        <div id="calc-result-box" class="p-5 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase text-emerald-800 flex items-center gap-1.5">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    <span>शिफारस केलेला सर्वोत्तम पर्याय (Recommended Action)</span>
                </span>
                <span class="text-sm font-black text-emerald-900 bg-emerald-200/80 px-3 py-1 rounded-full" id="calc-extra-profit">
                    +₹५,७५० जास्तीचा नफा
                </span>
            </div>

            <div class="text-sm leading-relaxed" id="calc-explanation">
                तुमच्या <strong>२५ क्विंटल कांद्यासाठी</strong> कोपरगाव स्थानिक बाजारापेक्षा <strong>लासलगाव APMC</strong> मध्ये माल नेणे अधिक फायदेशीर आहे. वाहतूक खर्च ₹१,५०० वजा जाता तुम्हाला <strong>₹५,७५० निव्वळ जास्तीचा नफा</strong> मिळेल.
            </div>
        </div>

    </div>

    <!-- 2. LIVE APMC RATES TABLE -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-black text-slate-900">महाराष्ट्र बाजार समिती दर (Live Commodity Rates)</h2>
                <p class="text-xs text-slate-500">कोपरगाव, नाशिक व विदर्भ परिसरातील ताजे व्यवहार</p>
            </div>
            <button onclick="location.reload()" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>अपडेट करा</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="text-xs uppercase bg-slate-100 text-slate-600 font-extrabold">
                    <tr>
                        <th class="py-3 px-4 rounded-l-xl">पीक / Commodity</th>
                        <th class="py-3 px-4">बाजार समिती (Market)</th>
                        <th class="py-3 px-4"><?= __t('min_price') ?></th>
                        <th class="py-3 px-4"><?= __t('max_price') ?></th>
                        <th class="py-3 px-4 font-black text-emerald-950"><?= __t('modal_price') ?></th>
                        <th class="py-3 px-4 rounded-r-xl">दिशा (Trend)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($mandiRows)): ?>
                        <?php foreach ($mandiRows as $row): ?>
                            <tr class="hover:bg-slate-50/80 transition font-medium">
                                <td class="py-4 px-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span><?= htmlspecialchars($row['name']) ?></span>
                                </td>
                                <td class="py-4 px-4 text-slate-600"><?= htmlspecialchars($row['market']) ?></td>
                                <td class="py-4 px-4 text-slate-500">₹<?= htmlspecialchars($row['min']) ?></td>
                                <td class="py-4 px-4 text-slate-500">₹<?= htmlspecialchars($row['max']) ?></td>
                                <td class="py-4 px-4 font-black text-base text-emerald-800">₹<?= htmlspecialchars($row['modal']) ?> / Q</td>
                                <td class="py-4 px-4">
                                    <?php if ($row['trend'] === 'up'): ?>
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full flex items-center gap-1 w-max">
                                            ▲ <?= htmlspecialchars($row['percent']) ?>
                                        </span>
                                    <?php elseif ($row['trend'] === 'down'): ?>
                                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold text-xs rounded-full flex items-center gap-1 w-max">
                                            ▼ <?= htmlspecialchars($row['percent']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold text-xs rounded-full flex items-center gap-1 w-max">
                                            ▬ 0.0%
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500 text-xs">
                                बाजार भाव लोड होत आहेत...
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
    function calculateMandiProfit() {
        const comm = document.getElementById('calc-commodity').value;
        const qty = parseFloat(document.getElementById('calc-quantity').value) || 10;
        const extraBadge = document.getElementById('calc-extra-profit');
        const expl = document.getElementById('calc-explanation');

        if (comm === 'onion') {
            const extraPerQ = 250;
            const transport = 1200;
            const netGain = Math.round((extraPerQ * qty) - transport);
            extraBadge.innerText = `+₹${netGain.toLocaleString('en-IN')} जास्तीचा नफा`;
            expl.innerHTML = `तुमच्या <strong>${qty} क्विंटल कांद्यासाठी</strong> कोपरगावपेक्षा <strong>लासलगाव APMC</strong> मध्ये माल नेणे अधिक फायदेशीर आहे. वाहतूक खर्च ₹${transport} वजा जाता तुम्हाला <strong>₹${netGain.toLocaleString('en-IN')} निव्वळ नफा</strong> वाढेल.`;
        } else if (comm === 'cotton') {
            const extraPerQ = 350;
            const netGain = Math.round(extraPerQ * qty);
            extraBadge.innerText = `+₹${netGain.toLocaleString('en-IN')} जास्तीचा नफा`;
            expl.innerHTML = `तुमच्या <strong>${qty} क्विंटल कापसासाठी</strong> गुरुवार पर्यंत वाट पहा. कोपरगाव APMC मध्ये आवक कमी असल्याने <strong>₹${netGain.toLocaleString('en-IN')} अतिरिक्त नफा</strong> मिळेल.`;
        } else if (comm === 'orange') {
            const extraPerQ = 400;
            const transport = 2500;
            const netGain = Math.round((extraPerQ * qty) - transport);
            extraBadge.innerText = `+₹${netGain.toLocaleString('en-IN')} जास्तीचा नफा`;
            expl.innerHTML = `तुमच्या <strong>${qty} क्विंटल संत्र्यासाठी</strong> नागपूर किंवा नाशिक बाजारात थेट विक्री केल्यास <strong>₹${netGain.toLocaleString('en-IN')} निव्वळ नफा</strong> वाढेल.`;
        } else {
            const netGain = Math.round(180 * qty);
            extraBadge.innerText = `+₹${netGain.toLocaleString('en-IN')} जास्तीचा नफा`;
            expl.innerHTML = `सोयाबीनसाठी लातूर बाजारपेठेत सर्वोत्तम दर मिळत आहे. <strong>₹${netGain.toLocaleString('en-IN')} अतिरिक्त फायदा</strong> होईल.`;
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
