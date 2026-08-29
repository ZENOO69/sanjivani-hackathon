<?php
/**
 * ====================================================================
 * FASAL - Mobile Floating Bottom Navigation Bar (Farmer-Friendly)
 * ====================================================================
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Mobile Bottom Navigation (Only visible on Phone / Mobile Screens) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-t border-emerald-100 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 py-2">
    <div class="grid grid-cols-5 gap-1 text-center">
        
        <a href="dashboard" class="flex flex-col items-center justify-center py-1 rounded-xl transition <?= in_array($currentPage, ['dashboard', 'index', '']) ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-500 font-medium' ?>">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5"><?= __t('nav_dashboard') ?></span>
        </a>

        <a href="advisory" class="flex flex-col items-center justify-center py-1 rounded-xl transition <?= $currentPage === 'advisory' ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-500 font-medium' ?>">
            <i data-lucide="sparkles" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5"><?= __t('nav_advisory') ?></span>
        </a>

        <a href="mandi" class="flex flex-col items-center justify-center py-1 rounded-xl transition <?= $currentPage === 'mandi' ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-500 font-medium' ?>">
            <i data-lucide="trending-up" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5"><?= __t('nav_mandi') ?></span>
        </a>

        <a href="community" class="flex flex-col items-center justify-center py-1 rounded-xl transition <?= $currentPage === 'community' ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-500 font-medium' ?>">
            <i data-lucide="truck" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5"><?= __t('nav_community') ?></span>
        </a>

        <a href="schemes" class="flex flex-col items-center justify-center py-1 rounded-xl transition <?= $currentPage === 'schemes' ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-500 font-medium' ?>">
            <i data-lucide="landmark" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5"><?= __t('nav_schemes') ?></span>
        </a>

    </div>
</nav>
