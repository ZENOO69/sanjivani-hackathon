<?php
define('FASAL_ROOT', __DIR__);
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/includes/factcheck_engine.php';

$trendingRumors = FactCheckEngine::getTrendingFactChecks();
?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- HERO HEADER: SATYA-RAKSHAK TRUTH RADAR -->
    <div class="relative rounded-3xl bg-gradient-to-br from-emerald-950 via-slate-900 to-emerald-900 text-white p-8 md:p-12 mb-8 shadow-2xl overflow-hidden border border-emerald-500/30">
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-0 p-8 opacity-10 font-mono text-8xl pointer-events-none select-none">🛡️</div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-4 border border-emerald-500/30 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>The Bad Reading • Real-Time Misinformation Defense</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4 text-white leading-tight">
                सत्य-रक्षक <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-200 to-amber-300">Truth Radar</span>
            </h1>
            
            <p class="text-slate-300 text-sm sm:text-base mb-6 leading-relaxed">
                व्हायरल अफवा, चुकीचे कृषी उपाय, बनावट शासकीय योजना व व्यावसायिक हेवेदाव्यातून केल्या जाणाऱ्या बोगस तक्रारींची वैज्ञानिक व शासकीय पडताळणी करणारी स्वयंचलित विश्वासार्ह यंत्रणा.
            </p>

            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-300">
                <span class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i> ICAR & MPKV राहुरी प्रमाणित
                </span>
                <span class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                    <i data-lucide="file-badge-2" class="w-4 h-4 text-amber-400"></i> शासन निर्णय (GR) थेट संदर्भ
                </span>
                <span class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                    <i data-lucide="bot" class="w-4 h-4 text-cyan-400"></i> सिंडिकेट बॉट स्मियर डिटेक्शन
                </span>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE VERIFICATION SCANNER BAR -->
    <div class="glass-card rounded-2xl p-6 sm:p-8 mb-10 shadow-lg border border-slate-200/80">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="search-check" class="w-6 h-6 text-emerald-600"></i>
                    व्हॉट्सअ‍ॅप मेसेज / बातमीची सत्यता तपासा
                </h2>
                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full">AI Fact-Check</span>
            </div>

            <p class="text-slate-600 text-xs sm:text-sm mb-4">
                कोणताही व्हायरल मेसेज, खतांची/औषधांची माहिती किंवा योजनेचा दावा येथे पेस्ट करा आणि काही सेकंदात अधिकृत वस्तुस्थिती जाणून घ्या:
            </p>

            <!-- Search Form -->
            <form id="factCheckForm" class="space-y-4">
                <div class="relative">
                    <textarea id="claimInput" rows="3" class="w-full rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all p-4 text-sm text-slate-800 placeholder-slate-400 shadow-inner" placeholder="उदा. कांद्यावर मीठ फवारल्यास करपा बरा होतो का? / नमो शेतकरी योजना बंद झाली का?"></textarea>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <!-- Preset Bad Reading Test Buttons -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-slate-500 font-medium">जलद चाचणी:</span>
                        <button type="button" onclick="setPreset('कांदा करपा रोगावर 5 किलो मीठ + युरिया फवारा रोग बरा होतो')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition-colors border border-slate-300">
                            🧂 मीठ फवारणी उपाय
                        </button>
                        <button type="button" onclick="setPreset('नमो शेतकरी महासन्मान निधी योजना बंद झाली असून पैसे कापले जाणार आहेत')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition-colors border border-slate-300">
                            🏛️ नमो शेतकरी योजना बंद
                        </button>
                        <button type="button" onclick="setPreset('गोदावरी कंपनीचे सोयाबीन बियाणे बोगस असून उगवण क्षमता शून्य आहे')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition-colors border border-slate-300">
                            🛡️ बोगस बियाणे तक्रार
                        </button>
                    </div>

                    <button type="submit" id="btnCheck" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-semibold px-6 py-2.5 rounded-xl shadow-md transition-all active:scale-95 text-sm">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        पडताळणी करा (Verify Now)
                    </button>
                </div>
            </form>

            <!-- Verification Live Result Container (Hidden by default) -->
            <div id="resultBox" class="mt-6 hidden transition-all duration-300"></div>
        </div>
    </div>

    <!-- TRENDING VERIFIED CLAIMS & RUMOR BUSTER RADAR -->
    <div class="mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="radio" class="w-6 h-6 text-rose-500 animate-pulse"></i>
                    सध्याच्या व्हायरल अफवा व वैज्ञानिक सत्य (Trending Fact-Checks)
                </h2>
                <p class="text-slate-600 text-sm mt-1">कोपरगाव व उत्तर महाराष्ट्र परिसरातील कृषी शास्त्रज्ञांनी फेटाळलेल्या खोट्या बातम्या</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold bg-rose-100 text-rose-800 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> 4 खोट्या अफवा निष्प्रभ
                </span>
                <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 1 प्रमाणित योजना
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($trendingRumors as $item): ?>
                <?php
                $isDangerous = $item['verdict'] === 'DANGEROUS_FAKE';
                $isFake = $item['verdict'] === 'FAKE';
                $isSmear = $item['verdict'] === 'QUARANTINED_SMEAR';
                $isVerified = $item['verdict'] === 'GOVERNMENT_VERIFIED';

                $cardBorder = $isDangerous ? 'border-rose-300 bg-rose-50/40' : ($isFake ? 'border-amber-300 bg-amber-50/40' : ($isSmear ? 'border-purple-300 bg-purple-50/40' : 'border-emerald-300 bg-emerald-50/40'));
                $badgeBg = $isDangerous ? 'bg-rose-600 text-white' : ($isFake ? 'bg-amber-600 text-white' : ($isSmear ? 'bg-purple-600 text-white' : 'bg-emerald-600 text-white'));
                $badgeText = $isDangerous ? 'धोकादायक खोटा उपाय (Dangerous Fake)' : ($isFake ? 'खोटी अफवा (Falsified Claim)' : ($isSmear ? 'बनावट तक्रार / सिंडिकेट हल्ला' : 'शासकीय प्रमाणित सत्य (Verified)'));
                ?>
                <div class="glass-card rounded-2xl p-6 border <?= $cardBorder ?> shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-xs font-bold px-3 py-1 rounded-full <?= $badgeBg ?> shadow-sm">
                                <?= $badgeText ?>
                            </span>
                            <span class="text-xs font-medium text-slate-500 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> <?= date('d M Y', strtotime($item['reported_date'])) ?>
                            </span>
                        </div>

                        <div class="mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($item['category']) ?></span>
                            <h3 class="text-base font-extrabold text-slate-900 mt-1 leading-snug">
                                "<?= htmlspecialchars($item['claim_mr']) ?>"
                            </h3>
                        </div>

                        <div class="bg-white/90 rounded-xl p-4 mb-4 border border-slate-200/80 shadow-inner">
                            <div class="flex items-center gap-1.5 text-xs font-bold <?= $isVerified ? 'text-emerald-700' : 'text-rose-700' ?> mb-1.5">
                                <i data-lucide="<?= $isVerified ? 'check-circle' : 'alert-triangle' ?>" class="w-4 h-4"></i>
                                <?= $isVerified ? 'अधिकृत वस्तुस्थिती (Official Fact):' : 'वैज्ञानिक सत्य व खंडन (Scientific Debunk):' ?>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                <?= htmlspecialchars($item['debunk_summary_mr']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1 text-slate-500 truncate max-w-[60%]">
                            <i data-lucide="building" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                            <span class="truncate" title="<?= htmlspecialchars($item['source']) ?>">
                                <?= htmlspecialchars($item['source']) ?>
                            </span>
                        </div>

                        <?php if ($item['shares_stopped'] > 0): ?>
                            <span class="text-emerald-700 font-semibold bg-emerald-100/80 px-2 py-0.5 rounded-md shrink-0">
                                <?= number_format($item['shares_stopped']) ?> शेअर्स थांबवले
                            </span>
                        <?php else: ?>
                            <span class="text-emerald-700 font-semibold bg-emerald-100/80 px-2 py-0.5 rounded-md shrink-0">
                                100% सुरक्षित
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ARCHITECTURAL DEFENSE EXPLANATION -->
    <div class="bg-slate-900 text-white rounded-3xl p-8 shadow-xl border border-slate-800">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 text-emerald-400 text-xs font-semibold mb-3 border border-slate-700">
                <i data-lucide="lock" class="w-3.5 h-3.5"></i> "The Bad Reading" Defense Framework
            </div>
            <h2 class="text-2xl font-bold mb-3">प्रणाली बदनामी व अफवांविरुद्ध कसा लढा देते? (How FASAL Fights Bad Readings)</h2>
            <p class="text-slate-300 text-sm leading-relaxed mb-6">
                जेव्हा कोणतीही खोटी माहिती किंवा हेतूपुरस्पर केलेली तक्रार प्लॅटफॉर्मवर येते, तेव्हा FASAL खालील 3-स्तरीय सत्य-प्रोटोकॉल राबवते:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold mb-2">1</div>
                    <h3 class="font-bold text-white mb-1">ICAR व विद्यापीठ ग्राउंडिंग</h3>
                    <p class="text-slate-400">अनधिकृत कीटकनाशके व घातक उपायांची CIBRC व MPKV राहुरीच्या शास्त्रीय नोंदींशी पडताळणी केली जाते.</p>
                </div>
                <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold mb-2">2</div>
                    <h3 class="font-bold text-white mb-1">शासकीय GR पडताळणी</h3>
                    <p class="text-slate-400">योजनांविषयी पसरवल्या जाणाऱ्या अफवा थेट अधिकृत महाडीबीटी व कृषी विभाग शासन निर्णयावरून फेटाळल्या जातात.</p>
                </div>
                <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold mb-2">3</div>
                    <h3 class="font-bold text-white mb-1">सिंडिकेट बॉट क्वारंटाईन</h3>
                    <p class="text-slate-400">एकाच आयपी किंवा बॉट समूहावरून प्रतिस्पर्धी शेतकऱ्याला/संस्थेला बदनाम करणाऱ्या खोट्या तक्रारींना तात्काळ क्वारंटाईन केले जाते.</p>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
function setPreset(text) {
    document.getElementById('claimInput').value = text;
    document.getElementById('factCheckForm').dispatchEvent(new Event('submit'));
}

document.getElementById('factCheckForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('claimInput').value.trim();
    if (!input) return;

    const btn = document.getElementById('btnCheck');
    const resultBox = document.getElementById('resultBox');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin inline-block mr-1.5">⏳</span> पडताळणी करत आहे...';
    resultBox.classList.remove('hidden');
    resultBox.innerHTML = `
        <div class="bg-slate-100 rounded-2xl p-6 text-center border border-slate-200">
            <div class="inline-block animate-spin text-3xl mb-2">🛡️</div>
            <p class="text-sm font-semibold text-slate-700">ICAR, MPKV राहुरी व शासकीय डेटाबेसमधून पडताळणी सुरू आहे...</p>
        </div>
    `;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch('api/factcheck.php?action=verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ query: input })
        });

        const data = await response.json();
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="shield-alert" class="w-4 h-4"></i> पडताळणी करा (Verify Now)';

        if (!data.success) {
            resultBox.innerHTML = `
                <div class="bg-rose-100 border border-rose-300 text-rose-800 p-4 rounded-xl text-sm">
                    ${data.message || 'त्रुटी आली. कृपया पुन्हा प्रयत्न करा.'}
                </div>
            `;
            if (window.lucide) lucide.createIcons();
            return;
        }

        const isDangerous = data.verdict === 'DANGEROUS_FAKE';
        const isFake = data.verdict === 'FAKE';
        const isSmear = data.verdict === 'QUARANTINED_SMEAR';
        const isVerified = data.verdict === 'GOVERNMENT_VERIFIED' || data.verdict === 'VERIFIED';

        const headerBg = isDangerous ? 'bg-rose-600' : (isFake ? 'bg-amber-600' : (isSmear ? 'bg-purple-600' : 'bg-emerald-600'));
        const verdictTitle = isDangerous ? '⚠️ अत्यंत धोकादायक खोटा उपाय' : (isFake ? '❌ खोटी अफवा' : (isSmear ? '🛡️ बनावट तक्रार / सिंडिकेट हल्ला' : '✅ शासकीय प्रमाणित सत्य'));

        const shareUrl = encodeURIComponent(data.whatsapp_share || '');

        resultBox.innerHTML = `
            <div class="bg-white rounded-2xl border-2 ${isDangerous ? 'border-rose-400' : (isFake ? 'border-amber-400' : 'border-emerald-400')} shadow-xl overflow-hidden animate-fadeIn">
                <div class="${headerBg} text-white p-4 sm:p-5 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🛡️</span>
                        <div>
                            <h3 class="font-extrabold text-base sm:text-lg">${verdictTitle}</h3>
                            <span class="text-xs text-white/90 font-medium">सत्यता निर्देशांक (Trust Score): ${data.trust_score}%</span>
                        </div>
                    </div>
                    <span class="text-xs bg-black/20 px-3 py-1 rounded-full font-semibold border border-white/20">
                        ${data.category || 'कृषी पडताळणी'}
                    </span>
                </div>

                <div class="p-6 space-y-4 text-slate-800">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">तपासलेला दावा (Claim):</span>
                        <p class="text-sm font-semibold text-slate-900 mt-0.5">"${data.matched_claim}"</p>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="text-xs font-bold text-emerald-800 flex items-center gap-1 mb-1">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i> वैज्ञानिक सत्य व विश्लेषण:
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed">${data.debunk_summary}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-lg bg-slate-100 border border-slate-200">
                            <span class="font-bold text-slate-500 block mb-0.5">🏛️ अधिकृत संदर्भ (Official Source):</span>
                            <span class="text-slate-800 font-semibold">${data.official_source}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-100 border border-slate-200">
                            <span class="font-bold text-slate-500 block mb-0.5">💡 सुरक्षित सल्ला (Safe Action):</span>
                            <span class="text-slate-800 font-medium">${data.recommendation}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <span class="text-xs text-slate-500">शेतकऱ्यांना जागृत करा व अफवांना आळा घाला:</span>
                        <a href="https://api.whatsapp.com/send?text=${shareUrl}" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-md active:scale-95">
                            <i data-lucide="share-2" class="w-3.5 h-3.5"></i> व्हॉट्सअ‍ॅपवर सत्य शेअर करा
                        </a>
                    </div>
                </div>
            </div>
        `;

        if (window.lucide) lucide.createIcons();
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="shield-alert" class="w-4 h-4"></i> पडताळणी करा (Verify Now)';
        resultBox.innerHTML = `
            <div class="bg-rose-100 border border-rose-300 text-rose-800 p-4 rounded-xl text-sm">
                पडताळणी दरम्यान तांत्रिक त्रुटी आली. कृपया पुन्हा प्रयत्न करा.
            </div>
        `;
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
