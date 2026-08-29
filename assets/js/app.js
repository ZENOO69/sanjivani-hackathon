/**
 * ====================================================================
 * FASAL - Client Reactivity & Farmer Voice Engine
 * ====================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // 2. Easy Mode Auto-Detection & Persistent State
    initEasyMode();

    // 3. Setup Voice Audio (Text-to-Speech)
    initVoicePlayer();

    // 4. Live IoT Telemetry Poller (every 20 seconds)
    initIoTSync();
});

/**
 * Easy Mode Management
 * Automatically switches to large typography on mobile devices or via toggle
 */
function initEasyMode() {
    const isMobile = window.innerWidth <= 768;
    const urlParams = new URLSearchParams(window.location.search);
    const easyParam = urlParams.get('easy');
    
    let isEasy = localStorage.getItem('fasal_easy_mode');

    if (easyParam !== null) {
        isEasy = (easyParam === '1' || easyParam === 'true') ? 'true' : 'false';
        localStorage.setItem('fasal_easy_mode', isEasy);
    } else if (isEasy === null && isMobile) {
        // Auto default to true on phone
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

/**
 * Voice Audio Reader (Web Speech API)
 * Reads out advisory messages in Marathi (mr-IN), Hindi (hi-IN), or English (en-IN)
 */
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
    
    // Choose appropriate voice/locale
    if (lang === 'mr') {
        utterance.lang = 'mr-IN';
    } else if (lang === 'hi') {
        utterance.lang = 'hi-IN';
    } else {
        utterance.lang = 'en-IN';
    }

    utterance.rate = 0.95; // Slightly slower for clear understanding
    utterance.pitch = 1.0;

    if (triggerBtn) {
        triggerBtn.classList.add('animate-pulse', 'ring-4', 'ring-emerald-300');
        const span = triggerBtn.querySelector('span');
        if (span) span.innerText = 'थांबवा (Stop) ⏹';
    }

    utterance.onend = () => {
        resetVoiceButtons();
    };

    utterance.onerror = () => {
        resetVoiceButtons();
    };

    currentUtterance = utterance;
    synth.speak(utterance);
}

function resetVoiceButtons() {
    document.querySelectorAll('[data-voice-text]').forEach(btn => {
        btn.classList.remove('animate-pulse', 'ring-4', 'ring-emerald-300');
        const span = triggerBtn = btn.querySelector('span');
        if (span) span.innerText = 'सल्ला ऐका 🔊';
    });
}

/**
 * IoT Live Telemetry Poller
 */
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
        } catch (err) {
            console.log('IoT Polling...', err);
        }
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
