<?php
if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}
$config = isset($GLOBALS['FASAL_CONFIG']) ? $GLOBALS['FASAL_CONFIG'] : require __DIR__ . '/../config.php';
?>
    <div class="h-20 md:h-0"></div>

    <footer class="bg-gradient-to-b from-slate-900 via-emerald-950 to-slate-950 text-slate-300 pt-12 pb-16 border-t border-emerald-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
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

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">केंद्रीत तालुके व बाजारपेठ</h4>
                    <p class="text-xs text-slate-400 mb-2">
                        कोपरगाव (Kopargaon), राहाता (Rahata), लासलगाव (Lasalgaon), संगमनेर, श्रीरामपूर, नाशिक आणि विदर्भ कृषी बाजार.
                    </p>
                    <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-amber-300">
                        📍 <strong>GPS स्थान:</strong> <?= htmlspecialchars($config['farm_location']['region_name']) ?> (<?= $config['farm_location']['latitude'] ?>° N, <?= $config['farm_location']['longitude'] ?>° E)
                    </div>
                </div>

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

            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p><?= htmlspecialchars($config['app']['footer_text']) ?></p>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 text-emerald-400 bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-800/60">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>SQLi • XSS • CSRF • DoS • DDoS Protected</span>
                    </span>
                    <button onclick="openBlackoutModal()" class="text-rose-400 hover:text-rose-300 underline font-bold">
                        ⚡ The Blackout Recovery Studio
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE DISASTER RECOVERY & "THE BLACKOUT" LIVE TEST STUDIO MODAL -->
    <div id="blackout-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-3xl w-full p-6 sm:p-8 text-white shadow-2xl space-y-6 relative overflow-hidden max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-start justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-500/20 border border-rose-500/40 rounded-full text-xs font-black text-rose-300 mb-2 uppercase">
                        <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                        <span>Disaster Recovery & Mid-Flight Blackout Studio</span>
                    </div>
                    <h3 class="text-2xl font-black text-white">The Blackout: Live Self-Healing Demo</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Simulate sudden data store corruption/wipe during in-flight operations. Watch FASAL failover to HA Shadow Cache and self-heal from Daily Snapshot + Write-Ahead Journal.
                    </p>
                </div>
                <button onclick="closeBlackoutModal()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Live Status Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Primary DB Store</div>
                    <div id="modal-db-status" class="font-extrabold text-emerald-400 text-sm mt-0.5">ONLINE & OPTIMAL</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">HA Shadow Cache</div>
                    <div class="font-extrabold text-blue-400 text-sm mt-0.5">ACTIVE (0ms Failover)</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Write-Ahead WAL</div>
                    <div id="modal-wal-count" class="font-extrabold text-amber-400 text-sm mt-0.5">Synced</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Daily Backups</div>
                    <div id="modal-backup-count" class="font-extrabold text-purple-400 text-sm mt-0.5">1/Day Auto Saved</div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="flex flex-wrap gap-3">
                <button onclick="runBlackoutSimulation()" id="btn-trigger-blackout" class="flex-1 min-w-[200px] py-3 px-4 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-extrabold rounded-2xl shadow-lg shadow-rose-900/30 transition flex items-center justify-center gap-2 text-xs uppercase tracking-wide">
                    <span>💥 1. Strike Blackout (Wipe DB Mid-Flight)</span>
                </button>

                <button onclick="runAutoHeal()" id="btn-trigger-heal" class="flex-1 min-w-[200px] py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-900/30 transition flex items-center justify-center gap-2 text-xs uppercase tracking-wide">
                    <span>🩹 2. Auto-Heal & Replay WAL</span>
                </button>

                <button onclick="triggerManualBackup()" class="py-3 px-4 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-2xl transition text-xs border border-slate-700">
                    📦 Take Daily Snapshot
                </button>
            </div>

            <!-- Live Terminal Output Console -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span class="font-bold flex items-center gap-1.5"><i data-lucide="terminal" class="w-3.5 h-3.5 text-emerald-400"></i> Live Telemetry & Audit Journal:</span>
                    <button onclick="clearConsoleLog()" class="hover:text-slate-200">Clear</button>
                </div>
                <div id="blackout-terminal" class="bg-black/90 rounded-2xl p-4 font-mono text-[11px] leading-relaxed text-slate-300 h-48 overflow-y-auto border border-slate-800 space-y-1">
                    <div class="text-emerald-400">[SYSTEM READY] Fasal Disaster Recovery Circuit Breaker active. All mutations mirrored to WAL Journal.</div>
                </div>
            </div>

        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
