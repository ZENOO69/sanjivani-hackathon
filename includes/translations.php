<?php
/**
 * ====================================================================
 * FASAL - Trilingual Localization System (Marathi, Hindi, English)
 * ====================================================================
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

class I18n {
    private static $translations = array(
        // Navigation & General
        'app_name' => array(
            'mr' => 'FASAL (फसल)',
            'hi' => 'FASAL (फसल)',
            'en' => 'FASAL',
        ),
        'tagline' => array(
            'mr' => 'स्मार्ट शेती, समृद्ध शेतकरी • निर्णय सल्लागार प्रणाली',
            'hi' => 'स्मार्ट खेती, समृद्ध किसान • निर्णय सलाहकार मंच',
            'en' => 'Smart Farming Decision-Intelligence & Advisory Platform',
        ),
        'nav_dashboard' => array(
            'mr' => 'मुख्य डॅशबोर्ड',
            'hi' => 'डैशबोर्ड',
            'en' => 'Dashboard',
        ),
        'nav_advisory' => array(
            'mr' => 'AI पीक डॉक्टर व सल्ला',
            'hi' => 'AI फसल डॉक्टर व सलाह',
            'en' => 'AI Crop Doctor & Advisory',
        ),
        'nav_mandi' => array(
            'mr' => 'थेट बाजार भाव (APMC)',
            'hi' => 'लाइव मंडी भाव (APMC)',
            'en' => 'Live Mandi Rates',
        ),
        'nav_community' => array(
            'mr' => 'ट्रॅक्टर व शेतमजूर बुकिंग',
            'hi' => 'ट्रैक्टर व मजदूर बुकिंग',
            'en' => 'Machinery & Labour',
        ),
        'nav_schemes' => array(
            'mr' => 'सरकारी योजना व अनुदान',
            'hi' => 'सरकारी योजनाएं व सब्सिडी',
            'en' => 'Govt Schemes & Subsidies',
        ),
        'nav_profile' => array(
            'mr' => 'माझी शेती प्रोफाइल',
            'hi' => 'मेरी खेत प्रोफाइल',
            'en' => 'Farm Profile',
        ),
        'nav_login' => array(
            'mr' => 'लॉगिन करा',
            'hi' => 'लॉग इन करें',
            'en' => 'Login',
        ),
        'nav_register' => array(
            'mr' => 'नवीन नोंदणी करा',
            'hi' => 'नया पंजीकरण',
            'en' => 'Register',
        ),
        'nav_logout' => array(
            'mr' => 'लॉगआउट',
            'hi' => 'लॉग आउट',
            'en' => 'Logout',
        ),
        'easy_mode' => array(
            'mr' => 'सुलभ मोड (मोठा मजकूर)',
            'hi' => 'सरल मोड (बड़ा टेक्स्ट)',
            'en' => 'Easy Farmer Mode',
        ),
        'listen_audio' => array(
            'mr' => 'सल्ला ऐका 🔊',
            'hi' => 'सलाह सुनें 🔊',
            'en' => 'Listen Audio 🔊',
        ),
        'stop_audio' => array(
            'mr' => 'आवाज थांबवा ⏹',
            'hi' => 'आवाज रोकें ⏹',
            'en' => 'Stop Audio ⏹',
        ),

        // IoT & Weather
        'soil_moisture' => array(
            'mr' => 'मातीतील ओलावा',
            'hi' => 'मिट्टी की नमी',
            'en' => 'Soil Moisture',
        ),
        'soil_raw_val' => array(
            'mr' => 'माती सेन्सर रिडिंग (A0)',
            'hi' => 'मिट्टी सेंसर मान (A0)',
            'en' => 'Soil Raw Reading',
        ),
        'field_temp' => array(
            'mr' => 'शेतातील तापमान',
            'hi' => 'खेत का तापमान',
            'en' => 'Field Temperature',
        ),
        'field_humidity' => array(
            'mr' => 'हवेतील आर्द्रता',
            'hi' => 'हवा में नमी',
            'en' => 'Air Humidity',
        ),
        'soil_status' => array(
            'mr' => 'जमिनीची सद्यस्थिती',
            'hi' => 'मिट्टी की स्थिति',
            'en' => 'Soil Condition',
        ),
        'status_dry' => array(
            'mr' => 'कोरडी (पाण्याची गरज)',
            'hi' => 'सूखी (पानी की जरूरत)',
            'en' => 'DRY (Water Needed)',
        ),
        'status_moderate' => array(
            'mr' => 'उत्तम / मध्यम ओलावा',
            'hi' => 'मध्यम (पर्याप्त नमी)',
            'en' => 'OPTIMAL / MODERATE',
        ),
        'status_wet' => array(
            'mr' => 'ओली (पाणी देऊ नका)',
            'hi' => 'गीली (पानी न दें)',
            'en' => 'WET (Do Not Irrigate)',
        ),
        'live_iot_connected' => array(
            'mr' => 'ESP8266 थेट सेन्सर सुरू आहे',
            'hi' => 'ESP8266 लाइव सेंसर सक्रिय',
            'en' => 'ESP8266 Live Telemetry Active',
        ),
        'weather_forecast' => array(
            'mr' => 'हवामान अंदाज (कोपरगाव परिसर)',
            'hi' => 'मौसम पूर्वानुमान (कोपरगांव क्षेत्र)',
            'en' => 'Weather Forecast (Kopargaon Region)',
        ),
        'rain_prob' => array(
            'mr' => 'पावसाची शक्यता',
            'hi' => 'बारिश की संभावना',
            'en' => 'Rain Probability',
        ),
        'wind_speed' => array(
            'mr' => 'वाऱ्याचा वेग',
            'hi' => 'हवा की गति',
            'en' => 'Wind Speed',
        ),

        // Decision Intelligence & Action
        'actionable_advisories' => array(
            'mr' => 'आजचे महत्त्वाचे कृती निर्णय (Actionable Decisions)',
            'hi' => 'आज के महत्वपूर्ण कार्य निर्णय (Actionable Decisions)',
            'en' => 'Today\'s Critical Actionable Decisions',
        ),
        'take_action' => array(
            'mr' => 'तातडीने कृती करा',
            'hi' => 'तुरंत कार्रवाई करें',
            'en' => 'Take Action Now',
        ),
        'before_after_title' => array(
            'mr' => 'तुमच्या शेतीतील फरक: पारंपारिक विरुद्ध FASAL स्मार्ट शेती',
            'hi' => 'आपके खेत में फर्क: पारंपरिक बनाम FASAL स्मार्ट खेती',
            'en' => 'Farming Impact: Traditional vs FASAL Intelligent Farming',
        ),
        'traditional_way' => array(
            'mr' => 'पूर्वीची पद्धत (अंदाजे निर्णय)',
            'hi' => 'पुरानी पद्धति (अंदाजे से निर्णय)',
            'en' => 'Traditional Guesswork',
        ),
        'fasal_way' => array(
            'mr' => 'FASAL प्लॅटफॉर्म (डेटा व AI आधारित निर्णय)',
            'hi' => 'FASAL प्लेटफॉर्म (डेटा व AI आधारित निर्णय)',
            'en' => 'FASAL Smart Intelligence',
        ),

        // Mandi Ticker & APMC
        'mandi_live_rates' => array(
            'mr' => 'महाराष्ट्र थेट APMC बाजार भाव',
            'hi' => 'महाराष्ट्र लाइव APMC मंडी भाव',
            'en' => 'Maharashtra Live APMC Mandi Rates',
        ),
        'min_price' => array(
            'mr' => 'किमान दर',
            'hi' => 'न्यूनतम भाव',
            'en' => 'Min Price',
        ),
        'max_price' => array(
            'mr' => 'कमाल दर',
            'hi' => 'अधिकतम भाव',
            'en' => 'Max Price',
        ),
        'modal_price' => array(
            'mr' => 'सरासरी दर',
            'hi' => 'औसत भाव',
            'en' => 'Average Price',
        ),
        'best_market_advice' => array(
            'mr' => 'केव्हा व कोठे विक्री करावी?',
            'hi' => 'कब और कहाँ बेचें?',
            'en' => 'When & Where to Sell?',
        ),

        // AI Doctor
        'ask_ai_title' => array(
            'mr' => 'AI शेती तज्ज्ञ व पीक रोग निदान',
            'hi' => 'AI कृषि विशेषज्ञ व फसल रोग निदान',
            'en' => 'AI Crop Doctor & Disease Diagnostics',
        ),
        'ask_ai_placeholder' => array(
            'mr' => 'तुमच्या पिकाची समस्या येथे लिहा किंवा बोला (उदा. कांद्याची पाने पिवळी पडत आहेत)...',
            'hi' => 'अपनी फसल की समस्या यहाँ लिखें या बोलें (उदा. प्याज की पत्तियां पीली हो रही हैं)...',
            'en' => 'Describe your crop issue or speak (e.g. Onion leaves turning yellow)...',
        ),
        'get_ai_solution' => array(
            'mr' => 'तज्ज्ञ सल्ला मिळवा 🚀',
            'hi' => 'विशेषज्ञ सलाह प्राप्त करें 🚀',
            'en' => 'Get Expert Advice 🚀',
        ),

        // Community & Machinery
        'rent_machinery' => array(
            'mr' => 'ट्रॅक्टर व अवजारे भाड्याने मिळवा',
            'hi' => 'ट्रैक्टर और कृषि यंत्र किराए पर लें',
            'en' => 'Rent Tractors & Implements',
        ),
        'book_labour' => array(
            'mr' => 'शेतमजूर टोळी संपर्क',
            'hi' => 'कृषि मजदूर टोली संपर्क',
            'en' => 'Book Farm Labour Groups',
        ),
        'call_owner' => array(
            'mr' => 'थेट फोन करा 📞',
            'hi' => 'सीधा फोन करें 📞',
            'en' => 'Call Now 📞',
        ),
        'whatsapp_chat' => array(
            'mr' => 'व्हॉट्सॲप मेसेज करा 💬',
            'hi' => 'व्हाट्सएप मैसेज 💬',
            'en' => 'WhatsApp Chat 💬',
        ),

        // Schemes
        'schemes_title' => array(
            'mr' => 'शेतकऱ्यांसाठी सक्रिय सरकारी योजना व थेट अनुदान',
            'hi' => 'किसानों के लिए सक्रिय सरकारी योजनाएं व सब्सिडी',
            'en' => 'Active Government Schemes & Subsidies for Farmers',
        ),
        'apply_scheme' => array(
            'mr' => 'योजनेसाठी अर्ज करा 📝',
            'hi' => 'योजना के लिए आवेदन करें 📝',
            'en' => 'Apply for Scheme 📝',
        ),
    );

    public static function getLang() {
        if (isset($_GET['lang']) && in_array($_GET['lang'], array('mr', 'hi', 'en'))) {
            $_SESSION['lang'] = $_GET['lang'];
        }
        if (isset($_SESSION['lang'])) {
            return $_SESSION['lang'];
        }
        if (isset($GLOBALS['FASAL_CONFIG']['app']['default_lang'])) {
            return $GLOBALS['FASAL_CONFIG']['app']['default_lang'];
        }
        return 'mr';
    }

    public static function t($key) {
        $lang = self::getLang();
        if (isset(self::$translations[$key][$lang])) {
            return self::$translations[$key][$lang];
        }
        if (isset(self::$translations[$key]['en'])) {
            return self::$translations[$key]['en'];
        }
        return $key;
    }

    public static function isEasyMode() {
        if (isset($_GET['easy'])) {
            $_SESSION['easy_mode'] = ($_GET['easy'] === '1' || $_GET['easy'] === 'true');
        }
        if (!isset($_SESSION['easy_mode'])) {
            $_SESSION['easy_mode'] = true;
        }
        return (bool)$_SESSION['easy_mode'];
    }
}

function __t($key) {
    return I18n::t($key);
}
