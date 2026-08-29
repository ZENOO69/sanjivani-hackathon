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
            'mr' => 'स्मार्ट शेती, समृद्ध शेतकरी • स्मार्ट कृषी मंच',
            'hi' => 'स्मार्ट खेती, समृद्ध किसान • स्मार्ट कृषि मंच',
            'en' => 'Smart Farming Decision-Intelligence Platform',
        ),
        'nav_dashboard' => array(
            'mr' => 'डॅशबोर्ड',
            'hi' => 'डैशबोर्ड',
            'en' => 'Dashboard',
        ),
        'nav_advisory' => array(
            'mr' => 'पीक सल्ला',
            'hi' => 'फसल सलाह',
            'en' => 'Crop Doctor',
        ),
        'nav_mandi' => array(
            'mr' => 'बाजार भाव',
            'hi' => 'मंडी भाव',
            'en' => 'Mandi Rates',
        ),
        'nav_community' => array(
            'mr' => 'यंत्र व मजूर',
            'hi' => 'मशीनरी व मजदूर',
            'en' => 'Machinery',
        ),
        'nav_schemes' => array(
            'mr' => 'शासकीय योजना',
            'hi' => 'सरकारी योजनाएं',
            'en' => 'Govt Schemes',
        ),
        'nav_factcheck' => array(
            'mr' => 'सत्य-रक्षक',
            'hi' => 'सत्य-रक्षक',
            'en' => 'Truth Radar',
        ),
        'nav_profile' => array(
            'mr' => 'माझी प्रोफाइल',
            'hi' => 'मेरी प्रोफाइल',
            'en' => 'Profile',
        ),
        'nav_login' => array(
            'mr' => 'लॉगिन',
            'hi' => 'लॉग इन',
            'en' => 'Login',
        ),
        'nav_register' => array(
            'mr' => 'नोंदणी',
            'hi' => 'पंजीकरण',
            'en' => 'Register',
        ),
        'nav_logout' => array(
            'mr' => 'लॉगआउट',
            'hi' => 'लॉग आउट',
            'en' => 'Logout',
        ),
        'easy_mode' => array(
            'mr' => 'सुलभ मोड',
            'hi' => 'सरल मोड',
            'en' => 'Easy Mode',
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

        // Fact Check (The Bad Reading) Translations
        'fc_hero_badge' => array(
            'mr' => 'The Bad Reading • रिअल-टाइम अफवा व चुकीची माहिती प्रतिबंधक यंत्रणा',
            'hi' => 'The Bad Reading • रियल-टाइम अफवाह व भ्रामक जानकारी सुरक्षा प्रणाली',
            'en' => 'The Bad Reading • Real-Time Misinformation Defense',
        ),
        'fc_hero_title_1' => array(
            'mr' => 'सत्य-रक्षक',
            'hi' => 'सत्य-रक्षक',
            'en' => 'Truth Radar',
        ),
        'fc_hero_title_2' => array(
            'mr' => 'Truth Radar',
            'hi' => 'Truth Radar',
            'en' => 'Fact-Check Engine',
        ),
        'fc_hero_desc' => array(
            'mr' => 'व्हायरल अफवा, चुकीचे कृषी उपाय, बनावट शासकीय योजना व व्यावसायिक हेवेदाव्यातून केल्या जाणाऱ्या बोगस तक्रारींची वैज्ञानिक व शासकीय पडताळणी करणारी स्वयंचलित विश्वासार्ह यंत्रणा.',
            'hi' => 'वायरल अफवाहें, गलत कृषि उपाय, फर्जी सरकारी योजनाएं और दुर्भावनापूर्ण शिकायतों की वैज्ञानिक व सरकारी पुष्टि करने वाला स्वचालित विश्वसनीय मंच।',
            'en' => 'Automated truth-verification system against viral agricultural rumors, fraudulent government schemes, unscientific remedies, and coordinated smear complaints.',
        ),
        'fc_badge_icar' => array(
            'mr' => 'ICAR व MPKV राहुरी प्रमाणित',
            'hi' => 'ICAR व MPKV राहुरी प्रमाणित',
            'en' => 'ICAR & MPKV Rahuri Verified',
        ),
        'fc_badge_gr' => array(
            'mr' => 'शासन निर्णय (GR) थेट संदर्भ',
            'hi' => 'सरकारी शासनादेश (GR) संदर्भ',
            'en' => 'Official Govt GR Direct Source',
        ),
        'fc_badge_bot' => array(
            'mr' => 'सिंडिकेट बॉट स्मियर डिटेक्शन',
            'hi' => 'सिंडिकेट बॉट स्मियर डिटेक्शन',
            'en' => 'Syndicate Bot Smear Detection',
        ),
        'fc_scanner_title' => array(
            'mr' => 'व्हॉट्सअ‍ॅप मेसेज / बातमीची सत्यता तपासा',
            'hi' => 'व्हाट्सएप संदेश / खबर की सत्यता जांचें',
            'en' => 'Verify WhatsApp Forwards & News Authenticity',
        ),
        'fc_scanner_desc' => array(
            'mr' => 'कोणताही व्हायरल मेसेज, खतांची/औषधांची माहिती किंवा योजनेचा दावा येथे पेस्ट करा आणि काही सेकंदात अधिकृत वस्तुस्थिती जाणून घ्या:',
            'hi' => 'कोई भी वायरल संदेश, खाद/दवा की सलाह या योजना का दावा यहाँ पेस्ट करें और कुछ ही सेकंड में आधिकारिक स्थिति जानें:',
            'en' => 'Paste any viral forward, fertilizer/pesticide remedy, or scheme claim to get instant government-certified truth:',
        ),
        'fc_placeholder' => array(
            'mr' => 'उदा. कांद्यावर मीठ फवारल्यास करपा बरा होतो का? / नमो शेतकरी योजना बंद झाली का?',
            'hi' => 'उदा. प्याज पर नमक छिड़कने से झुलसा रोग ठीक होता है क्या? / नमो शेतकरी योजना बंद हो गई क्या?',
            'en' => 'e.g. Does spraying salt cure onion blight? / Has the Namo Shetkari scheme been cancelled?',
        ),
        'fc_quick_test' => array(
            'mr' => 'जलद चाचणी:',
            'hi' => 'त्वरित परीक्षण:',
            'en' => 'Quick Presets:',
        ),
        'fc_preset_salt' => array(
            'mr' => '🧂 मीठ फवारणी उपाय',
            'hi' => '🧂 नमक छिड़काव उपाय',
            'en' => '🧂 Salt Spray Remedy',
        ),
        'fc_preset_scheme' => array(
            'mr' => '🏛️ नमो शेतकरी योजना बंद',
            'hi' => '🏛️ नमो शेतकरी योजना बंद',
            'en' => '🏛️ Namo Shetkari Scheme Rumor',
        ),
        'fc_preset_seed' => array(
            'mr' => '🛡️ बोगस बियाणे तक्रार',
            'hi' => '🛡️ फर्जी बीज शिकायत',
            'en' => '🛡️ Fake Seed Complaint',
        ),
        'fc_btn_verify' => array(
            'mr' => 'पडताळणी करा (Verify Now)',
            'hi' => 'सत्यापित करें (Verify Now)',
            'en' => 'Verify Claim Now',
        ),
        'fc_trending_title' => array(
            'mr' => 'सध्याच्या व्हायरल अफवा व वैज्ञानिक सत्य (Trending Fact-Checks)',
            'hi' => 'वर्तमान वायरल अफवाहें व वैज्ञानिक सत्य (Trending Fact-Checks)',
            'en' => 'Active Viral Rumors & Scientific Truth (Trending Fact-Checks)',
        ),
        'fc_trending_sub' => array(
            'mr' => 'कोपरगाव व उत्तर महाराष्ट्र परिसरातील कृषी शास्त्रज्ञांनी फेटाळलेल्या खोट्या बातम्या',
            'hi' => 'कोपरगांव व उत्तरी महाराष्ट्र क्षेत्र के कृषि वैज्ञानिकों द्वारा खारिज की गई भ्रामक खबरें',
            'en' => 'Debunked false claims and fraudulent agricultural advisories in Kopargaon and Maharashtra',
        ),
        'fc_stopped_shares' => array(
            'mr' => 'शेअर्स थांबवले',
            'hi' => 'शेयर रोके गए',
            'en' => 'Viral Shares Blocked',
        ),
        'fc_fully_safe' => array(
            'mr' => '100% सुरक्षित व प्रमाणित',
            'hi' => '100% सुरक्षित व प्रमाणित',
            'en' => '100% Safe & Verified',
        ),
        'fc_official_fact' => array(
            'mr' => 'अधिकृत वस्तुस्थिती (Official Fact):',
            'hi' => 'आधिकारिक वस्तुस्थिति (Official Fact):',
            'en' => 'Official Scientific Fact:',
        ),
        'fc_scientific_debunk' => array(
            'mr' => 'वैज्ञानिक सत्य व खंडन (Scientific Debunk):',
            'hi' => 'वैज्ञानिक सत्य व खंडन (Scientific Debunk):',
            'en' => 'Scientific Debunk & Truth:',
        ),
        'fc_share_whatsapp' => array(
            'mr' => 'व्हॉट्सअ‍ॅपवर सत्य शेअर करा',
            'hi' => 'व्हाट्सएप पर सत्य शेयर करें',
            'en' => 'Share Fact-Check on WhatsApp',
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
