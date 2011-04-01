<?php
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
