<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

class Sourcemap_Bitly {
    public static function shorten($url) {
        $params = array("login" => Kohana::config('apis')->bitly_account_name, 
            "apiKey" => Kohana::config('apis')->bitly_api_key,
            "longUrl" => $url, "format" => "json"
        );

        $base = "http://api.bitly.com/v3/shorten?";

        $request = $base . http_build_query($params); 

        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result);
        if($result->status_code == "200") {
            return $result->data->url;
        } else { 
            return false;
        }
    }
}
