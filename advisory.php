<?php
define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 flex-1">
    
    <div class="bg-gradient-to-r from-purple-700 via-indigo-800 to-emerald-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-950/15 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 opacity-20 text-9xl select-none">🔬</div>
        
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-extrabold text-purple-100">
                <span class="w-2 h-2 rounded-full bg-purple-300 animate-ping"></span>
                <span>Powered by Google Gemini 1.5 & Maharashtra Agronomy Engine</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                <?= __t('ask_ai_title') ?>
            </h1>
            <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                तुमच्या पिकावरील रोग किंवा किडीची लक्षणे सांगा अथवा बोला. आमची AI प्रणाली कोपरगाव व महाराष्ट्र कृषी विद्यापीठाच्या शिफारशींनुसार तातडीने अचूक उपाय देईल.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-6 space-y-6">
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-purple-600"></i>
                    <span>तुमचा प्रश्न किंवा लक्षणे येथे टाका</span>
                </h2>

                <form id="ai-query-form" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">पीक निवडा (Select Crop)</label>
                        <select id="ai-crop" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="कांदा (Onion)">कांदा (Onion)</option>
                            <option value="कापूस (Cotton)">कापूस (Cotton)</option>
                            <option value="ऊस (Sugarcane)">ऊस (Sugarcane)</option>
                            <option value="सोयाबीन (Soybean)">सोयाबीन (Soybean)</option>
                            <option value="डाळिंब (Pomegranate)">डाळिंब (Pomegranate)</option>
                            <option value="संत्रा (Orange)">संत्रा (Orange)</option>
                            <option value="गहू (Wheat)">गहू (Wheat)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">पिकाची लक्षणे (Describe Symptoms)</label>
                        <textarea id="ai-query-text" rows="4" required placeholder="<?= __t('ask_ai_placeholder') ?>" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none transition"></textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" id="voice-mic-btn" class="px-4 py-3.5 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 rounded-2xl font-bold text-xs flex items-center gap-2 transition" title="माईक द्वारे बोला">
                            <i data-lucide="mic" class="w-4 h-4 text-purple-600"></i>
                            <span>माईक द्वारे बोला 🎙️</span>
                        </button>

                        <button type="submit" id="ai-submit-btn" class="flex-1 py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-extrabold rounded-2xl shadow-lg shadow-purple-600/25 transition transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                            <span><?= __t('get_ai_solution') ?></span>
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>

                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <span class="text-xs font-bold text-slate-500 block">वारंवार येणाऱ्या समस्या (Quick Click):</span>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="setQuery('कांद्याची पाने पिवळी पडून शेंड्याकडून सुकत आहेत व पानांवर जांभळे चट्टे दिसत आहेत.')" class="text-xs bg-slate-100 hover:bg-purple-100 text-slate-800 hover:text-purple-900 px-3 py-1.5 rounded-xl border border-slate-200 font-medium transition">
                            🧅 कांदा करपा व पिवळेपणा
                        </button>
                        <button onclick="setQuery('कापसाची पाने गोळा होत आहेत, पानांखाली बारीक पांढऱ्या किडी व तुडतुडे दिसत आहेत.')" class="text-xs bg-slate-100 hover:bg-purple-100 text-slate-800 hover:text-purple-900 px-3 py-1.5 rounded-xl border border-slate-200 font-medium transition">
                            🌱 कापूस रसशोषक कीड
                        </button>
                        <button onclick="setQuery('डाळिंबाच्या फळांवर काळे तेलकट डाग पडत आहेत आणि फळे तडकून गळत आहेत.')" class="text-xs bg-slate-100 hover:bg-purple-100 text-slate-800 hover:text-purple-900 px-3 py-1.5 rounded-xl border border-slate-200 font-medium transition">
                            🍎 डाळिंब तेल्या रोग
                        </button>
                        <button onclick="setQuery('उसाच्या पानांवर तांबूस डाग पडून वाढ खुंटली आहे.')" class="text-xs bg-slate-100 hover:bg-purple-100 text-slate-800 hover:text-purple-900 px-3 py-1.5 rounded-xl border border-slate-200 font-medium transition">
                            🎋 ऊस तांबेरा व पोक्का बोईंग
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="lg:col-span-6 space-y-6">
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 min-h-[420px] flex flex-col justify-between" id="ai-response-container">
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-900 uppercase">AI तज्ज्ञ सल्ला व शिफारस (Prescription)</span>
                        </div>
                        <span id="ai-source-badge" class="text-[11px] font-semibold bg-purple-100 text-purple-900 px-2.5 py-0.5 rounded-full">
                            Gemini AI Ready
                        </span>
                    </div>

                    <div id="ai-output-box" class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-purple-50/40 p-5 rounded-2xl border border-purple-100/80">
                        🌿 <strong>नमस्कार शेतकरी मित्र!</strong><br><br>
                        डाव्या बाजूला तुमच्या पिकाची समस्या लिहा किंवा माईक द्वारे बोला. <br><br>
                        आमचे <strong>Gemini AI पीक डॉक्टर</strong> तुमच्या स्थानिक हवामान व माती परिस्थितीनुसार तातडीचे निदान व औषधांची अचूक मात्रा (Dosage) देतील.
                    </div>
                </div>

                <div id="ai-action-controls" class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <button id="ai-listen-btn" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 transition shadow-sm">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                        <span>हा सल्ला ऐका (Voice Output) 🔊</span>
                    </button>

                    <button onclick="window.print()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl flex items-center gap-2 transition">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>प्रिस्क्रिप्शन प्रिंट करा</span>
                    </button>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
    function setQuery(text) {
        document.getElementById('ai-query-text').value = text;
        document.getElementById('ai-query-form').dispatchEvent(new Event('submit'));
    }

    const micBtn = document.getElementById('voice-mic-btn');
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRec();
        recognition.lang = 'mr-IN';
        recognition.interimResults = false;

        micBtn.addEventListener('click', () => {
            recognition.start();
            micBtn.classList.add('bg-rose-100', 'text-rose-700', 'animate-pulse');
            micBtn.querySelector('span').innerText = 'ऐकत आहे... बोला (Listening)';
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            document.getElementById('ai-query-text').value = transcript;
            micBtn.classList.remove('bg-rose-100', 'text-rose-700', 'animate-pulse');
            micBtn.querySelector('span').innerText = 'माईक द्वारे बोला 🎙️';
        };

        recognition.onerror = () => {
            micBtn.classList.remove('bg-rose-100', 'text-rose-700', 'animate-pulse');
            micBtn.querySelector('span').innerText = 'माईक द्वारे बोला 🎙️';
        };
    } else {
        micBtn.style.display = 'none';
    }

    let currentRawResponse = "";
    document.getElementById('ai-query-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const query = document.getElementById('ai-query-text').value.trim();
        const crop  = document.getElementById('ai-crop').value;
        const submitBtn = document.getElementById('ai-submit-btn');
        const outputBox = document.getElementById('ai-output-box');
        const sourceBadge = document.getElementById('ai-source-badge');

        if (!query) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span>सल्ला तयार होत आहे...</span> <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>`;
        outputBox.innerHTML = `<div class="py-12 text-center text-purple-700 font-bold flex flex-col items-center gap-3">
            <div class="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
            <span>Google Gemini AI शेती तज्ज्ञ विश्लेषण करत आहे...</span>
        </div>`;

        try {
            const res = await fetch('api/gemini-ai', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({ query, crop, lang: '<?= I18n::getLang() ?>' })
            });
            const data = await res.json();
            if (data.success) {
                currentRawResponse = data.response;
                outputBox.innerText = data.response;
                sourceBadge.innerText = data.source;
                sourceBadge.className = 'text-[11px] font-semibold bg-emerald-100 text-emerald-900 px-2.5 py-0.5 rounded-full';
            } else {
                outputBox.innerText = "माफ करा, तांत्रिक अडचणीमुळे सल्ला मिळू शकला नाही. कृपया पुन्हा प्रयत्न करा.";
            }
        } catch (err) {
            outputBox.innerText = "नेटवर्क त्रुटी. कृपया इंटरनेट तपासा.";
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span><?= __t('get_ai_solution') ?></span> <i data-lucide="sparkles" class="w-4 h-4"></i>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });

    document.getElementById('ai-listen-btn').addEventListener('click', () => {
        if (!currentRawResponse) {
            currentRawResponse = document.getElementById('ai-output-box').innerText;
        }
        speakText(currentRawResponse, '<?= I18n::getLang() ?>', document.getElementById('ai-listen-btn'));
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
