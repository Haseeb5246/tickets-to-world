<?php
if (!function_exists('getAirlineCodeFromName')) {
    function getAirlineCodeFromName($airlineName) {
        $airlines = [
            'British Airways' => 'BA',
            'Kenya Airways' => 'KA',
            'Royal Air Maroc' => 'RAM',
            'KLM' => 'KLM',
            'Air France' => 'AF',
            'Turkish Airlines' => 'TK',
            'Emirates' => 'EK',
            'Qatar Airways' => 'QR',
            'Lufthansa' => 'LH',
            'Virgin Atlantic' => 'VS',
            'Rwandair Express' => 'WB',
            'Ethiopian Air Lines' => 'ET',
            'Swiss' => 'LX'
        ];
        return $airlines[$airlineName] ?? null;
    }
}

if (!function_exists('getAirlineLogo')) {
    function getAirlineLogo($code) {
        $baseUrl = 'assets/images/air-line/';
        $logoMap = [
            'BA' => 'british-airways.png',
            'KA' => 'kenya-airways.png',
            'RAM' => 'royal-air-maroc.png',
            'KLM' => 'klm.png',
            'AF' => 'air-france.png',
            'TK' => 'turkish-airlines.png',
            'EK' => 'emirates.png',
            'QR' => 'qatar-airways.png',
            'LH' => 'lufthansa.png',
            'VS' => 'virgin-atlantic.png',
            'WB' => 'rwandair.png',
            'ET' => 'ethiopian-airlines.png',
            'LX' => 'swiss.png'
        ];
        
        if (!$code) {
            return $baseUrl . 'default-airline.png';
        }
        
        return $baseUrl . ($logoMap[$code] ?? 'default-airline.png');
    }
}
?>
