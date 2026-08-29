<?php
define('FASAL_ROOT', __DIR__);
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/translations.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-emerald-50 via-white to-green-50/40 py-12 sm:py-20">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-300/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-24 w-96 h-96 bg-amber-300/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100/90 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-extrabold shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>महाराष्ट्र शेतकरी निर्णय-सल्लागार प्रणाली • Kopargaon Hackathon 2026</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                    केवळ आकडेवारी नाही, <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-emerald-700 via-green-600 to-amber-600 bg-clip-text text-transparent">
                        प्रत्यक्ष नफा देणारे शेती निर्णय!
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    हवामान, मातीतील ओलावा (IoT ESP8266), बाजार भाव (APMC) आणि AI पीक रोग निदान एकाच ठिकाणी. <strong>कधी पाणी द्यावे, केव्हा विकावे आणि कोणती फवारणी करावी</strong> हे ठरवा एका क्लिकवर!
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2 justify-center lg:justify-start">
                    <a href="dashboard" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-extrabold rounded-2xl shadow-xl shadow-emerald-600/30 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3 text-base">
                        <span>डॅशबोर्ड उघडा (Open Dashboard)</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    
                    <button onclick="toggleEasyMode()" class="w-full sm:w-auto px-6 py-4 bg-white hover:bg-amber-50 border-2 border-amber-400 text-amber-900 font-extrabold rounded-2xl shadow-sm transition flex items-center justify-center gap-2 text-base">
                        <i data-lucide="smartphone" class="w-5 h-5 text-amber-600"></i>
                        <span>सुलभ मोड (Easy Mode)</span>
                    </button>
                </div>

                <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs text-slate-500 font-medium">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>ESP8266 IoT सेन्सर इंटिग्रेशन</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Google Gemini AI पीक डॉक्टर</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>थेट ऑडिओ (Voice) सल्ला</span>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-sm sm:max-w-md">
                    
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-green-400 rounded-3xl transform rotate-2 scale-105 opacity-20 blur-lg"></div>

                    <div class="relative bg-white/95 backdrop-blur-xl border border-emerald-200/80 rounded-3xl p-6 shadow-2xl shadow-emerald-950/10 space-y-4">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                <span class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Live Kopargaon Farm</span>
                            </div>
                            <span class="text-[11px] font-semibold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">
                                ID: KOPARGAON_ESP8266_001
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gradient-to-br from-emerald-50 to-green-50/60 p-3.5 rounded-2xl border border-emerald-100 text-center">
                                <span class="text-[11px] font-bold text-emerald-800 block mb-1">मातीतील ओलावा</span>
                                <span class="text-2xl font-black text-emerald-950">38%</span>
                                <span class="text-[10px] text-emerald-700 block mt-0.5 font-semibold">मध्यम (MODERATE)</span>
                            </div>

                            <div class="bg-gradient-to-br from-amber-50 to-orange-50/60 p-3.5 rounded-2xl border border-amber-100 text-center">
                                <span class="text-[11px] font-bold text-amber-800 block mb-1">शेतातील तापमान</span>
                                <span class="text-2xl font-black text-amber-950">31.5 °C</span>
                                <span class="text-[10px] text-amber-700 block mt-0.5 font-semibold">हवामान: कोरडे</span>
                            </div>
                        </div>

                        <div class="bg-emerald-900 text-white p-4 rounded-2xl space-y-2 shadow-inner">
                            <div class="flex items-center justify-between text-xs text-emerald-300 font-bold">
                                <span>⚡ तात्काळ शिफारस (Actionable)</span>
                                <span class="px-2 py-0.5 bg-emerald-800 rounded text-[10px]">High Priority</span>
                            </div>
                            <p class="text-sm font-bold text-white leading-snug">
                                "उद्या सकाळी ६:०० वाजता कांदा पिकाला ४५ मिनिटे ठिबक सिंचन सुरू करा. दुपारी तापमान ३६°C जाण्याचा अंदाज."
                            </p>
                            <div class="pt-2 flex items-center justify-between">
                                <button data-voice-text="उद्या सकाळी सहा वाजता कांदा पिकाला पंचेचाळीस मिनिटे ठिबक सिंचन सुरू करा. दुपारी तापमान छत्तीस अंश सेल्सिअस जाण्याचा अंदाज आहे." class="text-xs text-amber-300 hover:text-white font-bold flex items-center gap-1">
                                    <i data-lucide="volume-2" class="w-4 h-4"></i>
                                    <span>सल्ला ऐका 🔊</span>
                                </button>
                                <a href="dashboard" class="text-xs bg-emerald-700 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-lg transition">
                                    सविस्तर पहा &rarr;
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Section 2: Before vs After Comparison Matrix -->
<section class="py-16 bg-white border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-black uppercase tracking-wider">
                <i data-lucide="scale" class="w-4 h-4 text-amber-700"></i>
                <span>Problem Statement Focus: Before vs After</span>
            </div>
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900">
                <?= __t('before_after_title') ?>
            </h2>
            <p class="text-slate-600 text-sm sm:text-base">
                FASAL प्लॅटफॉर्ममुळे शेतकऱ्याचे निर्णय कसे बदलतात आणि नफ्यात कशी भर पडते ते पहा:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-rose-50/60 rounded-3xl p-6 sm:p-8 border-2 border-rose-200/80 space-y-6 shadow-sm">
                <div class="flex items-center gap-3 pb-4 border-b border-rose-200">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center font-black text-lg">
                        ❌
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-rose-950"><?= __t('traditional_way') ?></h3>
                        <p class="text-xs text-rose-700">माहिती अभावी होणारे नुकसान</p>
                    </div>
                </div>

                <ul class="space-y-4 text-sm text-slate-700">
                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">अंदाजे सिंचन (Blind Irrigation):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">जमिनीतील ओलावा न मोजता अति पाणी दिल्याने वीज व पाण्याचा अपव्यय, मुळकूज रोग.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">बाजारपेठेची अनभिज्ञता (Mandi Guesswork):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">व्यापाऱ्याच्या दरावर विसंबून माल विकल्याने प्रति क्विंटल ₹२५० ते ₹५०० चा तोटा.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">उशिरा कीड नियंत्रण (Late Spraying):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">रोग पसरल्यावर भरमसाठ महागडी औषधे फवारून पिकाचे ३०% पर्यंत नुकसान.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">मजूर व यंत्र टंचाई (Labour Scarcity):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">काढणीच्या वेळी ट्रॅक्टर किंवा मजूर न मिळाल्याने शेतातच माल खराब होणे.</p>
                        </div>
                    </li>
                </ul>

                <div class="p-4 bg-white rounded-2xl border border-rose-200 text-rose-900 text-xs font-bold text-center">
                    सरासरी नुकसान: प्रति एकर ₹१२,००० ते ₹१८,००० ची घट
                </div>
            </div>

            <div class="bg-emerald-50/70 rounded-3xl p-6 sm:p-8 border-2 border-emerald-300 space-y-6 shadow-md relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">🌾</div>

                <div class="flex items-center gap-3 pb-4 border-b border-emerald-200">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md shadow-emerald-600/30">
                        ✅
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-emerald-950"><?= __t('fasal_way') ?></h3>
                        <p class="text-xs text-emerald-700">IoT सेन्सर, APMC डेटा व AI आधारित अचूक सल्ला</p>
                    </div>
                </div>

                <ul class="space-y-4 text-sm text-slate-800">
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">अचूक सेन्सर सिंचन (IoT Smart Irrigation):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">मातीचा ओलावा ३०% पेक्षा कमी होताच मोबाईलवर अलर्ट. ३५% पाणी व विजेची बचत.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">नफा वाढवणारी बाजार वेळ (Smart Selling):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">लासलगाव/कोपरगाव APMC मधील आगामी भावाचा अंदाज &rarr; प्रति क्विंटल +₹३५० अधिक नफा.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">AI पीक डॉक्टर (Gemini AI Diagnostics):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">लक्षणे दिसताच योग्य औषधाची शिफारस व मराठीत थेट ऑडिओ सल्ला.</p>
                        </div>
                    </li>

                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900">१-क्लिक ट्रॅक्टर व मजूर बुकिंग (1-Tap Dispatch):</strong>
                            <p class="text-xs text-slate-600 mt-0.5">परिसरातील उपलब्ध अवजारे व मजूर टोळ्यांशी थेट व्हॉट्सॲप व कॉलद्वारे संपर्क.</p>
                        </div>
                    </li>
                </ul>

                <div class="p-4 bg-emerald-600 text-white rounded-2xl shadow-md text-xs font-black text-center">
                    सरासरी फायदा: प्रति एकर +₹२२,५०० पर्यंत निव्वळ उत्पन्नात वाढ!
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Section 3: The 6 Core Data Streams -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-12 space-y-2">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                एकत्रित ६ मुख्य डेटा प्रवाह (Unified 6 Data Streams)
            </h2>
            <p class="text-slate-600 text-sm">
                विविध स्त्रोतांकडून आलेला डेटा एका सुसंगत निर्णयामध्ये रूपांतरित होतो:
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xl">
                    📟
                </div>
                <h3 class="text-base font-extrabold text-slate-900">१. थेट ESP8266 IoT सेन्सर्स</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    मातीचा ओलावा (A0 Soil Sensor), तापमान आणि हवेतील आर्द्रता (DHT11) रिअल-टाइम ट्रॅकिंग.
                </p>
            </div>

            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-black text-xl">
                    🌦️
                </div>
                <h3 class="text-base font-extrabold text-slate-900">२. अचूक हवामान व पाऊस अंदाज</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Open-Meteo हायपर-लोकल जीपीएस द्वारे आगामी ७ दिवसांचे पर्जन्यमान, वाऱ्याचा वेग व उष्णता अलर्ट.
                </p>
            </div>

            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black text-xl">
                    📈
                </div>
                <h3 class="text-base font-extrabold text-slate-900">३. महाराष्ट्र APMC थेट बाजार दर</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    कांदा, कापूस, संत्रा, सोयाबीन व डाळिंबाचे कोपरगाव, लासलगाव, नागपूर बाजारांमधील थेट दर व ट्रेंड्स.
                </p>
            </div>

            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-xl">
                    🤖
                </div>
                <h3 class="text-base font-extrabold text-slate-900">४. Gemini AI पीक रोग निदान</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    पिकाच्या लक्षणांवरून तात्काळ रोग निदान, योग्य रासायनिक/जैविक फवारणी शिफारस व मराठी ऑडिओ.
                </p>
            </div>

            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center font-black text-xl">
                    🚜
                </div>
                <h3 class="text-base font-extrabold text-slate-900">५. अवजारे व शेतमजूर पूल</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    भाड्याने ट्रॅक्टर, रोटाव्हेटर, हार्वेस्टर आणि काढणी मजुरांच्या टोळ्यांचे थेट संपर्क व दर.
                </p>
            </div>

            <div class="glass-card glass-card-hover p-6 rounded-3xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl">
                    🏛️
                </div>
                <h3 class="text-base font-extrabold text-slate-900">६. सरकारी योजना व थेट अनुदान</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    MahaDBT, PM-किसान, ठिबक सिंचन ८०% अनुदान आणि पीक विमा योजनेसाठी १-क्लिक मार्गदर्शन.
                </p>
            </div>

        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
