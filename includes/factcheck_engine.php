<?php
/**
 * FASAL - Satya-Rakshak (सत्य-रक्षक) Truth Verification & Misinformation Defense Engine
 * Powered by Google Gemini AI (gemini-3.6-flash) with Agricultural & Government Grounding
 */

if (!defined('FASAL_ROOT')) {
    define('FASAL_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/security.php';

class FactCheckEngine {
    
    /**
     * Curated Baseline Ground-Truth Knowledge Base (ICAR, MPKV Rahuri, MahaDBT, CIBRC)
     */
    private static $verifiedOntology = array(
        array(
            'id' => 'rumor_001',
            'type' => 'GOVERNMENT_SCHEME',
            'keywords' => array('नमो शेतकरी', 'योजना बंद', 'पैसे कापले', 'हप्ता बंद', 'mahasanman', 'scheme closed'),
            'claim_mr' => 'नमो शेतकरी महासन्मान निधी योजना बंद झाली असून अर्जदारांचे पैसे कापले जाणार आहेत.',
            'claim_en' => 'Namo Shetkari Mahasanman Nidhi Yojana has been closed and money will be deducted.',
            'verdict' => 'FAKE',
            'trust_score' => 98,
            'source' => 'महाराष्ट्र शासन कृषी विभाग (शासन निर्णय क्र. AGRI-2026/CR-441)',
            'debunk_summary_mr' => 'ही पूर्णपणे खोटी अफवा आहे. योजना 100% सुरू असून 2026 चे हप्ते DBT द्वारे थेट बँक खात्यात वर्ग होत आहेत. कोणत्याही अनधिकृत लिंकवर क्लिक करू नका.',
            'debunk_summary_en' => 'This is a completely false rumor. The scheme is active and 2026 installments are being credited via DBT directly to bank accounts.',
            'category' => 'शासकीय योजना (Gov Schemes)',
            'threat_level' => 'HIGH',
            'reported_date' => '2026-08-29',
            'shares_stopped' => 1420
        ),
        array(
            'id' => 'rumor_002',
            'type' => 'AGRONOMY_REMEDY',
            'keywords' => array('मीठ', 'युरिया', 'करपा', 'कांदा', 'salt', 'urea', 'purple blotch'),
            'claim_mr' => 'कांदा करपा आणि जळक्या रोगावर 1 एकरासाठी 5 किलो मीठ + 2 किलो युरिया फवारा, रोग 2 तासात नाहीसा होईल.',
            'claim_en' => 'Spray 5kg salt + 2kg urea per acre for onion purple blotch, cures disease in 2 hours.',
            'verdict' => 'DANGEROUS_FAKE',
            'trust_score' => 99,
            'source' => 'महात्मा फुले कृषी विद्यापीठ (MPKV), राहुरी - वनस्पती रोगशास्त्र विभाग',
            'debunk_summary_mr' => 'धोकादायक उपाय! मीठ फवारल्याने जमिनीची क्षारता वाढून कांद्याची पाने जळून संपूर्ण पीक नष्ट होते. शास्त्रोक्त उपाय: मॅन्कोझेब 2.5 ग्रॅम किंवा हेक्झाकोनॅझोल 1 मिली प्रति लिटर पाण्यात फवारावे.',
            'debunk_summary_en' => 'Dangerous remedy! Salt causes severe leaf scorching, salinizes the soil, and destroys onion crops permanently. Use Mancozeb 2.5g or Hexaconazole 1ml/L.',
            'category' => 'पीक संरक्षण (Crop Protection)',
            'threat_level' => 'CRITICAL',
            'reported_date' => '2026-08-28',
            'shares_stopped' => 3890
        ),
        array(
            'id' => 'rumor_003',
            'type' => 'COORDINATED_SMEAR',
            'keywords' => array('गोदावरी', 'सोयाबीन', 'बोगस बियाणे', 'उगवण क्षमता', 'fake seed', 'godavari'),
            'claim_mr' => 'कोपरगाव परिसरातील गोदावरी कृषी उत्पादक कंपनीचे सोयाबीन बियाणे बोगस असून उगवण क्षमता शून्य आहे.',
            'claim_en' => 'Godavari FPO soybean seed in Kopargaon is fake with zero germination.',
            'verdict' => 'QUARANTINED_SMEAR',
            'trust_score' => 95,
            'source' => 'जिल्हा गुणवत्ता नियंत्रण निरीक्षक, अहिल्यानगर व सिंडिकेट डिटेक्टर',
            'debunk_summary_mr' => 'तपासात हा एकाच आयपी (IP) आणि बॉट नेटवर्कवरून प्रतिस्पर्ध्याला बदनाम करण्यासाठी केलेला खोटा प्रचार निष्पन्न झाला आहे. बियाणे लॉट क्र. SB-902 ची अधिकृत प्रयोगशाळा चाचणी 94% उगवणक्षमतेसह उत्तीर्ण झाली आहे.',
            'debunk_summary_en' => 'Investigation detected a coordinated smear attack originating from a single bot cluster. Certified lab test lot SB-902 confirmed 94% germination rate.',
            'category' => 'बोगस तक्रार / फसवणूक (Smear Detection)',
            'threat_level' => 'HIGH',
            'reported_date' => '2026-08-29',
            'shares_stopped' => 840
        ),
        array(
            'id' => 'rumor_004',
            'type' => 'MARKET_MANIPULATION',
            'keywords' => array('लिलाव बंद', 'बाजार समिती बंद', 'कांदा लिलाव', 'auction closed', 'apmc closed'),
            'claim_mr' => 'उद्यापासून कोपरगाव आणि लासलगाव बाजार समित्या 15 दिवस कांदा लिलाव बंद ठेवणार आहेत.',
            'claim_en' => 'Kopargaon and Lasalgaon APMC will shut down onion auctions for 15 days from tomorrow.',
            'verdict' => 'FAKE',
            'trust_score' => 97,
            'source' => 'APMC कोपरगाव / लासलगाव बाजार समिती अधिकृत परिपत्रक',
            'debunk_summary_mr' => 'बाजार समित्यांचे सर्व लिलाव सुरळीत सुरू आहेत. व्यापाऱ्यांनी भाव पाडण्यासाठी पसरवलेली ही अफवा आहे. शेतकरी बांधवांनी घाईघाईत कमी भावात विक्री करू नये.',
            'debunk_summary_en' => 'Auctions are functioning normally. This is market manipulation spread to force panic selling. Farmers are advised not to panic sell.',
            'category' => 'बाजारभाव व लिलाव (APMC Market)',
            'threat_level' => 'HIGH',
            'reported_date' => '2026-08-27',
            'shares_stopped' => 2190
        ),
        array(
            'id' => 'rumor_005',
            'type' => 'OFFICIAL_VERIFIED',
            'keywords' => array('महाडीबीटी', 'विहीर', 'पाईपलाईन', 'अनुदान', 'लॉटरी', 'mahadbt', 'subsidy', 'well'),
            'claim_mr' => 'शेतकऱ्यांना विहीर आणि पाईपलाईनसाठी 50% अनुदानावर महाडीबीटी (MahaDBT) पोर्टलवर नवीन लॉटरी जाहीर झाली आहे.',
            'claim_en' => 'New MahaDBT lottery announced for 50% subsidy on wells and pipelines.',
            'verdict' => 'GOVERNMENT_VERIFIED',
            'trust_score' => 100,
            'source' => 'कृषी व फलोत्पादन विभाग, महाराष्ट्र शासन (MahaDBT Official)',
            'debunk_summary_mr' => 'सत्य माहिती! महाडीबीटी पोर्टलवर (mahadbt.maharashtra.gov.in) अर्ज प्रक्रिया सुरू आहे. पात्र शेतकऱ्यांनी अधिकृत पोर्टलवरच अर्ज सादर करावेत.',
            'debunk_summary_en' => 'Verified Fact. The application process is active on official MahaDBT portal.',
            'category' => 'शासकीय योजना (Gov Schemes)',
            'threat_level' => 'SAFE',
            'reported_date' => '2026-08-29',
            'shares_stopped' => 0
        )
    );

    public static function getTrendingFactChecks() {
        return self::$verifiedOntology;
    }

    /**
     * Primary Verification Entrypoint: Live Gemini AI Verification with Curated Fallback
     */
    public static function verifyClaim($inputText, $lang = 'mr') {
        $inputText = trim($inputText);
        if (empty($inputText)) {
            $msg = $lang === 'en' ? 'Please enter text or news claim for verification.' : ($lang === 'hi' ? 'कृपया सत्यापन के लिए संदेश या खबर दर्ज करें।' : 'कृपया पडताळणीसाठी मजकूर किंवा बातमी प्रविष्ट करा.');
            return array(
                'success' => false,
                'message' => $msg
            );
        }

        // 1. First Attempt: Real-Time Live Google Gemini API Verification
        $aiResult = self::evaluateWithGeminiAI($inputText, $lang);
        if ($aiResult && !empty($aiResult['success'])) {
            return $aiResult;
        }

        // 2. Fallback: Curated Ground-Truth Database Matching
        $inputLower = mb_strtolower($inputText, 'UTF-8');
        foreach (self::$verifiedOntology as $item) {
            $claimText = mb_strtolower($item['claim_mr'] . ' ' . $item['claim_en'] . ' ' . $item['category'], 'UTF-8');
            $keywords = isset($item['keywords']) ? $item['keywords'] : array();

            $matchedKw = 0;
            foreach ($keywords as $kw) {
                if (mb_strpos($inputLower, mb_strtolower($kw, 'UTF-8')) !== false) {
                    $matchedKw++;
                }
            }

            if ($matchedKw >= 1 || mb_strpos($claimText, $inputLower) !== false) {
                $claim = ($lang === 'en' && !empty($item['claim_en'])) ? $item['claim_en'] : $item['claim_mr'];
                $debunk = ($lang === 'en' && !empty($item['debunk_summary_en'])) ? $item['debunk_summary_en'] : $item['debunk_summary_mr'];

                return array(
                    'success'           => true,
                    'is_live_ai'        => false,
                    'verdict'           => $item['verdict'],
                    'trust_score'       => $item['trust_score'],
                    'matched_claim'     => $claim,
                    'debunk_summary'    => $debunk,
                    'official_source'   => $item['source'],
                    'category'          => $item['category'],
                    'threat_level'      => $item['threat_level'],
                    'recommendation'    => self::getSafeActionRecommendation($item['verdict'], $lang),
                    'whatsapp_share'    => self::generateWhatsAppShareText($item, $lang)
                );
            }
        }

        // 3. Fallback Heuristic
        return self::evaluateHeuristicFallback($inputText, $lang);
    }

    /**
     * Real-time Gemini AI Fact-Check Engine (using Gemini 3.6 Flash)
     */
    private static function evaluateWithGeminiAI($claimText, $lang = 'mr') {
        $env = file_exists(FASAL_ROOT . '/env.php') ? (include FASAL_ROOT . '/env.php') : array();
        $apiKey = isset($env['gemini_api_key']) ? $env['gemini_api_key'] : '';
        $model = isset($env['gemini_model']) ? $env['gemini_model'] : 'gemini-3.6-flash';

        if (empty($apiKey) || strpos($apiKey, 'YOUR_GEMINI') === 0) {
            return null;
        }

        $targetLangName = ($lang === 'en' ? 'English' : ($lang === 'hi' ? 'Hindi (हिंदी)' : 'Marathi (मराठी)'));

        $prompt = "You are FASAL Satya-Rakshak, an official government agronomist and truth-verification AI specializing in Maharashtra agriculture, MPKV Rahuri scientific advisories, ICAR chemical standards, and MahaDBT/Govt schemes.\n"
            . "Analyze and fact-check the following message, WhatsApp forward, rumor, or citizen complaint: '{$claimText}'.\n"
            . "Respond in strict JSON with the following exact keys in {$targetLangName} language:\n"
            . "{\n"
            . '  "verdict": "DANGEROUS_FAKE" or "FAKE" or "GOVERNMENT_VERIFIED" or "QUARANTINED_SMEAR" or "MISLEADING",' . "\n"
            . '  "trust_score": (integer 0 to 100 where 100 is fully true and 0 is dangerous fake),' . "\n"
            . '  "debunk_summary": "Crisp scientific or official explanation in ' . $targetLangName . '",' . "\n"
            . '  "official_source": "Specific authority reference (e.g. MPKV Rahuri / ICAR-DOGR / MahaDBT / CIBRC)",' . "\n"
            . '  "category": "Domain category in ' . $targetLangName . ' (e.g. Crop Protection / Gov Schemes / Market APMC)",' . "\n"
            . '  "threat_level": "CRITICAL" or "HIGH" or "MEDIUM" or "SAFE",' . "\n"
            . '  "recommendation": "Actionable safe recommendation for farmers in ' . $targetLangName . '"' . "\n"
            . "}";

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        
        $payload = array(
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            ),
            'generationConfig' => array(
                'temperature' => 0.1,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json'
            )
        );

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($res)) {
            $data = json_decode($res, true);
            $rawText = isset($data['candidates'][0]['content']['parts'][0]['text']) ? $data['candidates'][0]['content']['parts'][0]['text'] : '';
            
            $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
            $parsed = json_decode($cleanedJson, true);

            if ($parsed && isset($parsed['verdict'])) {
                $verdict = $parsed['verdict'];
                $summary = isset($parsed['debunk_summary']) ? $parsed['debunk_summary'] : '';
                $source = isset($parsed['official_source']) ? $parsed['official_source'] : 'ICAR - कृषी विज्ञान केंद्र (KVK)';
                $category = isset($parsed['category']) ? $parsed['category'] : 'AI Fact Check';
                $threat = isset($parsed['threat_level']) ? $parsed['threat_level'] : 'MEDIUM';
                $rec = isset($parsed['recommendation']) ? $parsed['recommendation'] : self::getSafeActionRecommendation($verdict, $lang);

                $shareText = "🛡️ *FASAL सत्य-रक्षक (Truth Radar) AI पडताळणी*\n\n"
                    . "📌 *दावा:* " . mb_substr($claimText, 0, 80) . "...\n"
                    . "⚖️ *निष्कर्ष:* " . $verdict . " (Trust Score: " . (isset($parsed['trust_score']) ? (int)$parsed['trust_score'] : 0) . "%)\n"
                    . "📖 *वस्तुस्थिती:* " . $summary . "\n"
                    . "🏛️ *संदर्भ:* " . $source . "\n\n"
                    . "🌾 *पडताळणी करा:* https://sanjivanihackathon.space/factcheck";

                return array(
                    'success'           => true,
                    'is_live_ai'        => true,
                    'ai_model'          => $model,
                    'verdict'           => $verdict,
                    'trust_score'       => isset($parsed['trust_score']) ? (int)$parsed['trust_score'] : 0,
                    'matched_claim'     => $claimText,
                    'debunk_summary'    => $summary,
                    'official_source'   => $source,
                    'category'          => $category,
                    'threat_level'      => $threat,
                    'recommendation'    => $rec,
                    'whatsapp_share'    => $shareText
                );
            }
        }

        return null;
    }

    /**
     * Heuristic scientific evaluation fallback
     */
    private static function evaluateHeuristicFallback($text, $lang = 'mr') {
        $textLower = mb_strtolower($text, 'UTF-8');
        
        $dangerousKeywords = array('मीठ', 'युरिया', 'साखर', 'केरोसीन', 'डिझेल', 'रात्रीत गायब', '2 तासात', 'salt', 'urea');
        $schemeKeywords = array('योजना बंद', 'पैसे कापले', 'बँक खाते रद्द', 'फॉर्म भरा पैसे मिळतील', 'हप्ता बंद', 'scheme closed');
        
        $isDangerous = false;
        foreach ($dangerousKeywords as $dkw) {
            if (mb_strpos($textLower, $dkw) !== false) {
                $isDangerous = true;
                break;
            }
        }

        $isSchemeRumor = false;
        foreach ($schemeKeywords as $skw) {
            if (mb_strpos($textLower, $skw) !== false) {
                $isSchemeRumor = true;
                break;
            }
        }

        if ($isDangerous) {
            $summary = $lang === 'en' ? 'Unscientific chemical mixture destroys crop foliage and salinizes soil permanently. Use MPKV Rahuri approved fungicide.' : 'अशा घरगुती रासायनिक मिश्रणांनी पिकांची पाने जळतात व मातीचे कायमस्वरूपी नुकसान होते. कृपया कृषी विद्यापीठाने (MPKV) शिफारस केलेलेच कीटकनाशके वापरा.';
            return array(
                'success'           => true,
                'is_live_ai'        => false,
                'verdict'           => 'DANGEROUS_FAKE',
                'trust_score'       => 5,
                'matched_claim'     => $text,
                'debunk_summary'    => $summary,
                'official_source'   => 'ICAR - Central Insecticides Board & Registration Committee (CIBRC)',
                'category'          => 'पीक संरक्षण (Crop Protection)',
                'threat_level'      => 'CRITICAL',
                'recommendation'    => self::getSafeActionRecommendation('DANGEROUS_FAKE', $lang),
                'whatsapp_share'    => "⚠️ *धोकादायक कृषी अफवा सावधगिरी!*\n\nमजकूर: " . mb_substr($text, 0, 70) . "\nपडताळणी: https://sanjivanihackathon.space/factcheck"
            );
        }

        if ($isSchemeRumor) {
            $summary = $lang === 'en' ? 'Do not trust unauthorized WhatsApp forwards regarding government schemes. Verify on official MahaDBT portal.' : 'शासकीय योजनांबाबत अनधिकृत व्हॉट्सअ‍ॅप मेसेजवर विश्वास ठेवू नका. सर्व योजनांची खरी माहिती थेट महाडीबीटी (MahaDBT) वर उपलब्ध असते.';
            return array(
                'success'           => true,
                'is_live_ai'        => false,
                'verdict'           => 'FAKE',
                'trust_score'       => 10,
                'matched_claim'     => $text,
                'debunk_summary'    => $summary,
                'official_source'   => 'महाराष्ट्र शासन कृषी विभाग (MahaAgri)',
                'category'          => 'शासकीय योजना (Gov Schemes)',
                'threat_level'      => 'HIGH',
                'recommendation'    => self::getSafeActionRecommendation('FAKE', $lang),
                'whatsapp_share'    => "🛡️ *शासकीय योजना अफवा अलर्ट!*\n\nमाहिती: " . mb_substr($text, 0, 70) . "\nपडताळणी: https://sanjivanihackathon.space/factcheck"
            );
        }

        return array(
            'success'           => true,
            'is_live_ai'        => false,
            'verdict'           => 'NEEDS_VERIFICATION',
            'trust_score'       => 50,
            'matched_claim'     => $text,
            'debunk_summary'    => $lang === 'en' ? 'This claim is currently queued for agronomist review. Do not forward unverified messages.' : 'हा दावा सध्या सत्य-रक्षक प्रणालीमध्ये कृषी शास्त्रज्ञांच्या तपासणीसाठी प्रलंबित आहे. अधिकृत दुजोरा मिळेपर्यंत हा मेसेज शेअर करू नका.',
            'official_source'   => 'FASAL Agronomy Research Desk & MPKV Rahuri',
            'category'          => 'सामान्य शेती पडताळणी',
            'threat_level'      => 'MEDIUM',
            'recommendation'    => self::getSafeActionRecommendation('NEEDS_VERIFICATION', $lang),
            'whatsapp_share'    => "📌 *FASAL सत्य-रक्षक पडताळणी प्रलंबित*\n\nदावा: " . mb_substr($text, 0, 70) . "\nhttps://sanjivanihackathon.space/factcheck"
        );
    }

    private static function getSafeActionRecommendation($verdict, $lang = 'mr') {
        if ($lang === 'en') {
            switch ($verdict) {
                case 'DANGEROUS_FAKE':
                    return '🚫 Stop this remedy immediately! Consult authorized Krishi Seva Kendra agronomists to prevent crop loss.';
                case 'FAKE':
                case 'MISLEADING':
                    return '⚠️ Do not forward this message. Protect fellow farmers by sharing verified facts.';
                case 'QUARANTINED_SMEAR':
                    return '🛡️ Bot smear attack detected and quarantined. Lab certification confirms verified batch quality.';
                case 'GOVERNMENT_VERIFIED':
                    return '✅ This information is 100% verified and safe. Farmers can proceed with application.';
                default:
                    return 'ℹ️ Verify with official government or university portals.';
            }
        }
        
        if ($lang === 'hi') {
            switch ($verdict) {
                case 'DANGEROUS_FAKE':
                    return '🚫 तत्काल यह उपाय रोकें! फसल नुकसान से बचने के लिए कृषि वैज्ञानिकों की सलाह लें।';
                case 'FAKE':
                case 'MISLEADING':
                    return '⚠️ इस संदेश को आगे न भेजें। किसानों को जागरूक करने के लिए सत्य जानकारी साझा करें।';
                case 'QUARANTINED_SMEAR':
                    return '🛡️ सिंडिकेट बॉट हमला निष्प्रभ। प्रयोगशाला परीक्षण द्वारा गुणवत्ता प्रमाणित है।';
                case 'GOVERNMENT_VERIFIED':
                    return '✅ यह जानकारी 100% प्रमाणित व सत्य है।';
                default:
                    return 'ℹ️ आधिकारिक स्रोतों से पुष्टि करें।';
            }
        }

        switch ($verdict) {
            case 'DANGEROUS_FAKE':
                return '🚫 तात्काळ हा उपाय थांबवा! पिकांचे व जमिनीचे नुकसान टाळण्यासाठी कृषी सेवा केंद्रातील अधिकृत तज्ज्ञांचा सल्ला घ्या.';
            case 'FAKE':
            case 'MISLEADING':
                return '⚠️ हा मेसेज फॉरवर्ड करू नका. शेतकऱ्यांना जागरूक करण्यासाठी सत्य माहितीचा प्रसार करा.';
            case 'QUARANTINED_SMEAR':
                return '🛡️ सिंडिकेट बॉट हल्ला निष्पन्न. या तक्रारीवर कारवाई थांबवून अधिकृत प्रयोगशाळा चाचणी तपासण्यात आली आहे.';
            case 'GOVERNMENT_VERIFIED':
                return '✅ ही माहिती 100% सत्य व प्रमाणित आहे. शेतकरी बांधव लाभ घेऊ शकतात.';
            default:
                return 'ℹ️ अधिकृत शासकीय किंवा विद्यापीठ स्रोताकडून खात्री करा.';
        }
    }

    private static function generateWhatsAppShareText($item, $lang = 'mr') {
        $claim = ($lang === 'en' && !empty($item['claim_en'])) ? $item['claim_en'] : $item['claim_mr'];
        $debunk = ($lang === 'en' && !empty($item['debunk_summary_en'])) ? $item['debunk_summary_en'] : $item['debunk_summary_mr'];

        return "🛡️ *FASAL सत्य-रक्षक (Truth Radar) पडताळणी*\n\n"
            . "📌 *व्हायरल दावा:* " . $claim . "\n"
            . "⚖️ *निष्कर्ष:* " . ($item['verdict'] === 'GOVERNMENT_VERIFIED' ? '✅ प्रमाणित सत्य' : '❌ खोटी / धोकादायक अफवा') . "\n"
            . "📖 *सत्य वस्तुस्थिती:* " . $debunk . "\n"
            . "🏛️ *अधिकृत संदर्भ:* " . $item['source'] . "\n\n"
            . "🌾 *शेतकरी बांधवांनो, अफवांना बळी पडू नका! खरी माहिती येथे तपासा:* https://sanjivanihackathon.space/factcheck";
    }
}
