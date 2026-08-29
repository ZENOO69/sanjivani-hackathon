<?php
define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';

$schemes = [
    [
        'title'       => 'MahaDBT ठिबक व तुषार सिंचन ८०% अनुदान',
        'dept'        => 'महाराष्ट्र शासन कृषी विभाग',
        'benefit'     => '८०% पर्यंत थेट बँक खात्यात अनुदान (Subsidy)',
        'eligibility' => '७/१२, ८-अ उतारा व आधार कार्ड असलेले अल्प व अत्यल्प भूधारक शेतकरी.',
        'deadline'    => '३० सप्टेंबर २०२६ पर्यंत अर्ज सुरू',
        'portal_url'  => 'https://mahadbt.maharashtra.gov.in/',
        'badge'       => 'High Subsidy',
        'icon'        => 'droplets',
    ],
    [
        'title'       => 'पंतप्रधान कृषी सन्मान निधी (PM-Kisan) + नमो शेतकरी योजना',
        'dept'        => 'केंद्र व महाराष्ट्र शासन संयुक्त',
        'benefit'     => 'वार्षिक ₹१२,००० थेट खात्यात (₹६००० केंद्र + ₹६००० राज्य)',
        'eligibility' => 'सर्व पात्र शेतकरी ज्यांचे e-KYC पूर्ण आहे.',
        'deadline'    => 'सक्रिय (पुढील हप्ता लवकरच)',
        'portal_url'  => 'https://pmkisan.gov.in/',
        'badge'       => 'Direct Cash',
        'icon'        => 'banknote',
    ],
    [
        'title'       => 'कृषी यांत्रिकीकरण उप-अभियान (ट्रॅक्टर व अवजारे ५०% अनुदान)',
        'dept'        => 'कृषी व शेतकरी कल्याण मंत्रालय',
        'benefit'     => 'ट्रॅक्टर, रोटाव्हेटर, पॉवर टिलरवर १.२५ लाखांपर्यंत अनुदान',
        'eligibility' => 'वैयक्तिक शेतकरी किंवा महिला शेतकरी गट (SHG).',
        'deadline'    => 'लॉटरी पद्धतीने निवड चालू',
        'portal_url'  => 'https://mahadbt.maharashtra.gov.in/',
        'badge'       => '50% Subsidy',
        'icon'        => 'truck',
    ],
    [
        'title'       => 'प्रधानमंत्री पीक विमा योजना (१ रुपयात पीक विमा)',
        'dept'        => 'महाराष्ट्र शासन',
        'benefit'     => 'दुष्काळ, अतिवृष्टी किंवा रोगराईमुळे नुकसान झाल्यास संपूर्ण भरपाई',
        'eligibility' => 'खरीप व रब्बी हंगामातील सर्व नोंदणीकृत शेतकरी.',
        'deadline'    => 'हंगामानुसार मुदत',
        'portal_url'  => 'https://pmfby.gov.in/',
        'badge'       => 'Crop Insurance',
        'icon'        => 'shield-check',
    ],
];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 flex-1">
    
    <div class="bg-gradient-to-r from-teal-700 via-emerald-800 to-green-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-teal-950/15 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 opacity-20 text-9xl select-none">🏛️</div>
        
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-extrabold text-teal-100">
                <span class="w-2 h-2 rounded-full bg-teal-300 animate-ping"></span>
                <span>महाराष्ट्र शासन व केंद्र सरकारच्या सक्रिय योजना</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                <?= __t('schemes_title') ?>
            </h1>
            <p class="text-xs sm:text-sm text-teal-100 leading-relaxed">
                अनुदान मिळवणे आता सोपे! तुमच्या शेतासाठी पात्र असलेल्या योजनांची संपूर्ण माहिती, आवश्यक कागदपत्रे व १-क्लिक अधिकृत पोर्टल लिंक.
            </p>
        </div>
    </div>

    <!-- Schemes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($schemes as $s): ?>
            <div class="glass-card glass-card-hover rounded-3xl p-6 sm:p-8 space-y-5 flex flex-col justify-between border-t-4 border-t-teal-600">
                
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                            <?= Security::escape($s['dept']) ?>
                        </span>
                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 font-extrabold text-[10px] rounded-full uppercase">
                            <?= Security::escape($s['badge']) ?>
                        </span>
                    </div>

                    <h3 class="text-lg font-black text-slate-900 leading-snug">
                        <?= Security::escape($s['title']) ?>
                    </h3>

                    <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-950 text-xs font-bold">
                        🎁 फायदा: <?= Security::escape($s['benefit']) ?>
                    </div>

                    <div class="space-y-1 text-xs text-slate-600">
                        <p><strong>पात्रता:</strong> <?= Security::escape($s['eligibility']) ?></p>
                        <p class="text-amber-800 font-semibold"><strong>मुदत:</strong> <?= Security::escape($s['deadline']) ?></p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button onclick="alert('आवश्यक कागदपत्रे:\n१. ७/१२ व ८-अ उतारा\n२. आधार कार्ड व बँक पासबुक\n३. मोबाईल नंबर लिंक असणे आवश्यक')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        कागदपत्रे यादी 📄
                    </button>

                    <a href="<?= htmlspecialchars($s['portal_url']) ?>" target="_blank" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <span><?= __t('apply_scheme') ?></span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
