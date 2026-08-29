<?php
/**
 * ====================================================================
 * FASAL - Shared Footer Component
 * ====================================================================
 */
$config = isset($GLOBALS['FASAL_CONFIG']) ? $GLOBALS['FASAL_CONFIG'] : require __DIR__ . '/../config.php';
?>
    <!-- Spacer for mobile floating navigation bar -->
    <div class="h-20 md:h-0"></div>

    <!-- Main Footer -->
    <footer class="bg-gradient-to-b from-slate-900 via-emerald-950 to-slate-950 text-slate-300 pt-12 pb-16 border-t border-emerald-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <!-- Col 1: Brand & Mission -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-green-400 flex items-center justify-center text-white text-xl font-black">
                            🌱
                        </div>
                        <span class="text-2xl font-black text-white tracking-tight"><?= htmlspecialchars($config['app']['name']) ?></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        <?= htmlspecialchars($config['app']['tagline']) ?>
                    </p>
                    <div class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>IoT Telemetry: <?= htmlspecialchars($config['iot']['device_hash']) ?></span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">महत्त्वाचे विभाग (Modules)</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="dashboard" class="hover:text-emerald-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> <?= __t('nav_dashboard') ?></a></li>
                        <li><a href="advisory" class="hover:text-emerald-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> <?= __t('nav_advisory') ?></a></li>
                        <li><a href="mandi" class="hover:text-emerald-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> <?= __t('nav_mandi') ?></a></li>
                        <li><a href="community" class="hover:text-emerald-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> <?= __t('nav_community') ?></a></li>
                        <li><a href="schemes" class="hover:text-emerald-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i> <?= __t('nav_schemes') ?></a></li>
                    </ul>
                </div>

                <!-- Col 3: Maharashtra APMC & Weather Focus -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">केंद्रीत तालुके व बाजारपेठ</h4>
                    <p class="text-xs text-slate-400 mb-2">
                        कोपरगाव (Kopargaon), राहाता (Rahata), लासलगाव (Lasalgaon), संगमनेर, श्रीरामपूर, नाशिक आणि विदर्भ कृषी बाजार.
                    </p>
                    <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-amber-300">
                        📍 <strong>GPS स्थान:</strong> <?= htmlspecialchars($config['farm_location']['region_name']) ?> (<?= $config['farm_location']['latitude'] ?>° N, <?= $config['farm_location']['longitude'] ?>° E)
                    </div>
                </div>

                <!-- Col 4: Farmer Helpline & Emergency Support -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">शेतकरी हेल्पलाइन (Emergency)</h4>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-emerald-900/50 border border-emerald-700/60 text-emerald-200">
                            <div class="font-bold text-white">किसान कॉल सेंटर (Toll-Free):</div>
                            <a href="tel:18001801551" class="text-emerald-300 font-extrabold text-sm hover:underline">📞 1800-180-1551</a>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-300">
                            <div>कोपरगाव कृषी उत्पन्न बाजार समिती:</div>
                            <span class="font-bold text-white">02423-222245</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Line -->
            <div class="pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
                <p><?= htmlspecialchars($config['app']['footer_text']) ?></p>
            </div>
        </div>
    </footer>

    <!-- App Scripts -->
    <script src="assets/js/app.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
