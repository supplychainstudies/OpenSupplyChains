<?php
# Copyright (C) Sourcemap 2011
# This program is free software: you can redistribute it and/or modify it under the terms
# of the GNU Affero General Public License as published by the Free Software Foundation,
# either version 3 of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License along with this
# program. If not, see <http://www.gnu.org/licenses/>.

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
