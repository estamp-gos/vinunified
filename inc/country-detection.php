<?php
// inc/country-detection.php - IP Based Country Detection (FIXED for ALL EU countries)

function getUserCountry() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Localhost detection
    if ($ip == '127.0.0.1' || $ip == '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
        // For testing EU countries, change this temporarily to 'PT' or 'IE'
        return 'PK';
    }
    
    // Try API 1: ipwho.is (free, reliable)
    $api_url = "https://ipwho.is/{$ip}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['country_code']) && strlen($data['country_code']) == 2) {
            return $data['country_code'];
        }
    }
    
    // Try API 2: ipapi.co (backup)
    $api_url2 = "https://ipapi.co/{$ip}/country/";
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $api_url2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
    $country = curl_exec($ch2);
    curl_close($ch2);
    
    if ($country && strlen($country) == 2) {
        return trim($country);
    }
    
    // Try API 3: Cloudflare Trace (most reliable for Pakistan)
    $api_url3 = "https://www.cloudflare.com/cdn-cgi/trace";
    $ch3 = curl_init();
    curl_setopt($ch3, CURLOPT_URL, $api_url3);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_TIMEOUT, 5);
    $trace = curl_exec($ch3);
    curl_close($ch3);
    
    if ($trace && preg_match('/loc=(\w{2})/', $trace, $matches)) {
        return $matches[1];
    }
    
    // Default fallback
    return 'PK';
}

function getPricingByCountry($country) {
    // Default USD pricing
    $pricing = [
        'basic' => '$29',
        'premium' => '$59',
        'ultimate' => '$89'
    ];
    
    // Complete list of EU countries (should show EUR)
    $euCountries = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 
        'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 
        'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
    ];
    
    // Country-specific pricing
    $countryPricing = [
        // North America
        'US' => ['basic' => '$29', 'premium' => '$59', 'ultimate' => '$89'],
        'CA' => ['basic' => 'C$39', 'premium' => 'C$79', 'ultimate' => 'C$119'],
        
        // South Asia
        'PK' => ['basic' => '₨ 8,000', 'premium' => '₨ 16,000', 'ultimate' => '₨ 24,000'],
        'IN' => ['basic' => '₹2,499', 'premium' => '₹4,999', 'ultimate' => '₹7,499'],
        'BD' => ['basic' => '৳ 3,500', 'premium' => '৳ 7,000', 'ultimate' => '৳ 10,500'],
        'LK' => ['basic' => 'LKR 10,500', 'premium' => 'LKR 21,000', 'ultimate' => 'LKR 31,500'],
        'NP' => ['basic' => 'NPR 3,800', 'premium' => 'NPR 7,600', 'ultimate' => 'NPR 11,400'],
        
        // Europe (Non-EU)
        'GB' => ['basic' => '£23', 'premium' => '£47', 'ultimate' => '£71'],
        'CH' => ['basic' => 'CHF 26', 'premium' => 'CHF 53', 'ultimate' => 'CHF 79'],
        'NO' => ['basic' => 'NOK 310', 'premium' => 'NOK 620', 'ultimate' => 'NOK 930'],
        
        // Middle East
        'AE' => ['basic' => 'AED 106', 'premium' => 'AED 216', 'ultimate' => 'AED 327'],
        'SA' => ['basic' => 'SAR 109', 'premium' => 'SAR 221', 'ultimate' => 'SAR 334'],
        'QA' => ['basic' => 'QAR 106', 'premium' => 'QAR 215', 'ultimate' => 'QAR 324'],
        'KW' => ['basic' => 'KWD 8.9', 'premium' => 'KWD 18', 'ultimate' => 'KWD 27'],
        
        // Australia & NZ
        'AU' => ['basic' => 'A$45', 'premium' => 'A$89', 'ultimate' => 'A$135'],
        'NZ' => ['basic' => 'NZ$48', 'premium' => 'NZ$95', 'ultimate' => 'NZ$145'],
        
        // East Asia
        'JP' => ['basic' => '¥4,300', 'premium' => '¥8,600', 'ultimate' => '¥13,000'],
        'KR' => ['basic' => '₩39,000', 'premium' => '₩78,000', 'ultimate' => '₩118,000'],
        'SG' => ['basic' => 'SGD 39', 'premium' => 'SGD 79', 'ultimate' => 'SGD 119'],
        'MY' => ['basic' => 'RM 135', 'premium' => 'RM 270', 'ultimate' => 'RM 405'],
        
        // South America
        'BR' => ['basic' => 'R$ 160', 'premium' => 'R$ 320', 'ultimate' => 'R$ 480'],
        'AR' => ['basic' => 'ARS 29,000', 'premium' => 'ARS 58,000', 'ultimate' => 'ARS 87,000'],
        'CL' => ['basic' => 'CLP 27,000', 'premium' => 'CLP 54,000', 'ultimate' => 'CLP 81,000'],
        
        // Africa
        'ZA' => ['basic' => 'R 540', 'premium' => 'R 1,080', 'ultimate' => 'R 1,620'],
        'NG' => ['basic' => '₦ 45,000', 'premium' => '₦ 90,000', 'ultimate' => '₦ 135,000'],
        'KE' => ['basic' => 'KES 3,800', 'premium' => 'KES 7,600', 'ultimate' => 'KES 11,400'],
        
        // Russia & CIS
        'RU' => ['basic' => '₽ 2,600', 'premium' => '₽ 5,200', 'ultimate' => '₽ 7,800'],
        'UA' => ['basic' => '₴ 1,200', 'premium' => '₴ 2,400', 'ultimate' => '₴ 3,600'],
    ];
    
    // Check for exact match first
    if (isset($countryPricing[$country])) {
        return $countryPricing[$country];
    }
    
    // Check if country is in EU - show EUR
    if (in_array($country, $euCountries)) {
        return [
            'basic' => '€27',
            'premium' => '€55',
            'ultimate' => '€82'
        ];
    }
    
    // Default fallback to USD
    return $pricing;
}
?>