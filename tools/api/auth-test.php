<?php

define('APIURL', 'http://sourcemap.local/services/');

define('APIKEY', 'keygoeshere');
define('APISECRET', 'secretgoeshere');

date_default_timezone_set('America/New_York');

function sourcemap_api_auth_date($time=null) {
    if(!$time) $time = time();
    return date('r', $time);
}

function sourcemap_api_auth_token($apikey, $apisecret, $date) {
    return md5(sprintf('%s-%s-%s', $date, $apikey, $apisecret));
}

function sourcemap_api_auth_headers($apikey, $apisecret) {
    $hdrs = array();
    $hdrs[] = 'X-Sourcemap-API-Key: '.$apikey;
    $date = sourcemap_api_auth_date();
    $hdrs[] = 'Date: '.$date;
    $apitoken = sourcemap_api_auth_token($apikey, $apisecret, $date);
    $hdrs[] = 'X-Sourcemap-API-Token: '.$apitoken;
    return $hdrs;
}

if(php_sapi_name() === 'cli') {
    
    $ch = curl_init(APIURL);
    $hdrs = sourcemap_api_auth_headers(APIKEY, APISECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    print curl_exec($ch);
} else  die('Command line only.');
