/**
 * FASAL - Client Reactivity, Farmer Voice Engine & Disaster Recovery Studio
 */

document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    initEasyMode();
    initVoicePlayer();
    initIoTSync();
    checkIntegrityLive();
});

// Helper: Get CSRF token from meta tag
function getCsrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

// In-Flight Transaction Queue (Preserved across client storage)
const InFlightQueue = {
    getKey: () => 'fasal_inflight_tx_queue',
    getAll: () => {
        try {
            return JSON.parse(localStorage.getItem(InFlightQueue.getKey())) || [];
        } catch(e) { return []; }
    },
    add: (tx) => {
        const list = InFlightQueue.getAll();
        list.push({ ...tx, queued_at: new Date().toISOString(), status: 'BUFFERED_IN_FLIGHT' });
        localStorage.setItem(InFlightQueue.getKey(), JSON.stringify(list));
    },
    clear: () => localStorage.removeItem(InFlightQueue.getKey())
};

// Easy Mode Management
function initEasyMode() {
    const isMobile = window.innerWidth <= 768;
    const urlParams = new URLSearchParams(window.location.search);
    const easyParam = urlParams.get('easy');
    let isEasy = localStorage.getItem('fasal_easy_mode');

    if (easyParam !== null) {
        isEasy = (easyParam === '1' || easyParam === 'true') ? 'true' : 'false';
        localStorage.setItem('fasal_easy_mode', isEasy);
    } else if (isEasy === null && isMobile) {
        isEasy = 'true';
        localStorage.setItem('fasal_easy_mode', 'true');
    }

    if (isEasy === 'true') {
        document.body.classList.add('easy-mode');
        updateEasyModeToggles(true);
    } else {
        document.body.classList.remove('easy-mode');
        updateEasyModeToggles(false);
    }
}

function toggleEasyMode() {
    const current = document.body.classList.contains('easy-mode');
    const newState = !current;
    if (newState) {
        document.body.classList.add('easy-mode');
        localStorage.setItem('fasal_easy_mode', 'true');
    } else {
        document.body.classList.remove('easy-mode');
        localStorage.setItem('fasal_easy_mode', 'false');
    }
    updateEasyModeToggles(newState);
}

function updateEasyModeToggles(active) {
    document.querySelectorAll('.easy-mode-btn').forEach(btn => {
        if (active) {
            btn.classList.add('bg-amber-500', 'text-white');
            btn.classList.remove('bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-amber-500', 'text-white');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        }
    });
}

// Voice Audio Reader (Web Speech API)
let synth = window.speechSynthesis;
let currentUtterance = null;

function initVoicePlayer() {
    document.querySelectorAll('[data-voice-text]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const textToSpeak = btn.getAttribute('data-voice-text');
            const lang = btn.getAttribute('data-lang') || 'mr';
            speakText(textToSpeak, lang, btn);
        });
    });
}

function speakText(text, lang = 'mr', triggerBtn = null) {
    if (!('speechSynthesis' in window)) {
        alert('Your browser does not support Voice playback.');
        return;
    }

    if (synth.speaking) {
        synth.cancel();
        resetVoiceButtons();
        return;
    }

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang === 'mr' ? 'mr-IN' : (lang === 'hi' ? 'hi-IN' : 'en-IN');
    utterance.rate = 0.95;
    utterance.pitch = 1.0;

    if (triggerBtn) {
        triggerBtn.classList.add('animate-pulse', 'ring-4', 'ring-emerald-300');
        const span = triggerBtn.querySelector('span');
        if (span) span.innerText = 'थांबवा (Stop) ⏹';
    }

    utterance.onend = () => resetVoiceButtons();
    utterance.onerror = () => resetVoiceButtons();
    currentUtterance = utterance;
    synth.speak(utterance);
}

function resetVoiceButtons() {
    document.querySelectorAll('[data-voice-text]').forEach(btn => {
        btn.classList.remove('animate-pulse', 'ring-4', 'ring-emerald-300');
        const span = btn.querySelector('span');
        if (span) span.innerText = 'सल्ला ऐका 🔊';
    });
}

// IoT Live Telemetry Poller
function initIoTSync() {
    const iotWidget = document.getElementById('iot-live-widget');
    if (!iotWidget) return;

    setInterval(async () => {
        try {
            const res = await fetch('api/iot-sync?action=latest');
            if (res.ok) {
                const data = await res.json();
                if (data && data.success) {
                    updateTelemetryDOM(data.data);
                }
            }
        } catch (err) {}
    }, 20000);
}

function updateTelemetryDOM(data) {
    const tempEl = document.getElementById('val-temperature');
    const humEl  = document.getElementById('val-humidity');
    const soilEl = document.getElementById('val-soil-moisture');
    const rawEl  = document.getElementById('val-soil-raw');
    const statEl = document.getElementById('val-soil-status');
    const timeEl = document.getElementById('val-last-synced');

    if (tempEl && data.temperature) tempEl.innerText = data.temperature + ' °C';
    if (humEl && data.humidity) humEl.innerText = data.humidity + ' %';
    if (soilEl && data.soil_moisture) soilEl.innerText = data.soil_moisture + ' %';
    if (rawEl && data.soil_raw) rawEl.innerText = data.soil_raw;
    if (statEl && data.soil_status) {
        statEl.innerText = data.soil_status;
        if (data.soil_status.toUpperCase().includes('DRY')) {
            statEl.className = 'px-3 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-xs';
        } else if (data.soil_status.toUpperCase().includes('WET')) {
            statEl.className = 'px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-xs';
        } else {
            statEl.className = 'px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs';
        }
    }
    if (timeEl && data.recorded_at) timeEl.innerText = data.recorded_at;
}

// -------------------------------------------------------------
// DISASTER RECOVERY & "THE BLACKOUT" LIVE SIMULATOR
// -------------------------------------------------------------
function openBlackoutModal() {
    const modal = document.getElementById('blackout-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        checkIntegrityLive();
    }
}

function closeBlackoutModal() {
    const modal = document.getElementById('blackout-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function appendConsoleLog(msg, type = 'info') {
    const term = document.getElementById('blackout-terminal');
    if (!term) return;
    const line = document.createElement('div');
    const ts = new Date().toLocaleTimeString();
    
    if (type === 'danger') {
        line.className = 'text-rose-400 font-bold';
    } else if (type === 'success') {
        line.className = 'text-emerald-400 font-bold';
    } else if (type === 'warn') {
        line.className = 'text-amber-400';
    } else {
        line.className = 'text-slate-300';
    }

    line.innerText = `[${ts}] ${msg}`;
    term.appendChild(line);
    term.scrollTop = term.scrollHeight;
}

function clearConsoleLog() {
    const term = document.getElementById('blackout-terminal');
    if (term) term.innerHTML = '';
}

async function checkIntegrityLive() {
    try {
        const res = await fetch('api/blackout-sim?action=status');
        if (res.ok) {
            const json = await res.json();
            if (json.success) {
                const dbStat = document.getElementById('modal-db-status');
                const walCount = document.getElementById('modal-wal-count');
                const bckCount = document.getElementById('modal-backup-count');

                if (dbStat) {
                    if (json.integrity.status === 'BLACKOUT_DEGRADED') {
                        dbStat.innerText = 'CORRUPTED / WIPED';
                        dbStat.className = 'font-extrabold text-rose-500 text-sm mt-0.5 animate-pulse';
                    } else {
                        dbStat.innerText = 'ONLINE & OPTIMAL';
                        dbStat.className = 'font-extrabold text-emerald-400 text-sm mt-0.5';
                    }
                }
                if (walCount) walCount.innerText = `${json.integrity.wal_records} Mutations Synced`;
                if (bckCount) bckCount.innerText = `${json.backups.length} Daily Snapshot(s)`;
            }
        }
    } catch(e) {}
}

async function runBlackoutSimulation() {
    const btn = document.getElementById('btn-trigger-blackout');
    if (btn) btn.disabled = true;

    appendConsoleLog('💥 STRIKING BLACKOUT: Simulating sudden primary database corruption mid-flight...', 'danger');
    InFlightQueue.add({
        action: 'add_machinery',
        equipment: 'Mahindra 575 DI + Seed Drill',
        user: 'Kisan Vikas Samiti'
    });

    try {
        const res = await fetch('api/blackout-sim?action=simulate_blackout', {
            method: 'POST',
            headers: { 'X-CSRF-Token': getCsrfToken() }
        });
        const data = await res.json();
        if (data.logs) {
            data.logs.forEach(l => {
                appendConsoleLog(`(+${l.time}ms) ${l.msg}`, l.stage === 'BLACKOUT' ? 'danger' : 'warn');
            });
        }
        checkIntegrityLive();
    } catch (err) {
        appendConsoleLog('Circuit Breaker engaged on network level.', 'warn');
    } finally {
        if (btn) btn.disabled = false;
    }
}

async function runAutoHeal() {
    const btn = document.getElementById('btn-trigger-heal');
    if (btn) btn.disabled = true;

    appendConsoleLog('🩹 INITIATING AUTO-HEAL: Rebuilding schema and replaying Write-Ahead Journal...', 'warn');

    try {
        const res = await fetch('api/blackout-sim?action=auto_heal', {
            method: 'POST',
            headers: { 'X-CSRF-Token': getCsrfToken() }
        });
        const data = await res.json();
        if (data.logs) {
            data.logs.forEach(l => {
                appendConsoleLog(`(+${l.time}ms) ${l.msg}`, l.stage === 'VERIFIED' ? 'success' : 'info');
            });
        }
        InFlightQueue.clear();
        checkIntegrityLive();
    } catch (err) {
        appendConsoleLog('Error invoking Auto-Heal engine: ' + err.message, 'danger');
    } finally {
        if (btn) btn.disabled = false;
    }
}

async function triggerManualBackup() {
    appendConsoleLog('📦 Taking instant Daily Database Snapshot...', 'info');
    try {
        const res = await fetch('api/blackout-sim?action=create_backup', {
            method: 'POST',
            headers: { 'X-CSRF-Token': getCsrfToken() }
        });
        const data = await res.json();
        if (data.success) {
            appendConsoleLog(`✅ Snapshot saved: ${data.filename} (${data.total_records} records, Checksum: ${data.checksum.substring(0, 12)}...)`, 'success');
            checkIntegrityLive();
        }
    } catch(err) {
        appendConsoleLog('Snapshot generation failed: ' + err.message, 'danger');
    }
}
