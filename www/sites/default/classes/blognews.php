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

class Blognews {

    const NEWSURL = 'http://blog.sourcemap.com/api/get_recent_posts/';
    const PAGEURL = 'http://blog.sourcemap.com/api/get_page_index/';

    public static function cache_key($num) {
        return sprintf('blog-news-num-%02d', $num);
    }

    public static function fetchnews($num=20, $cache=true) {
        $num = max(0, min(50, $num));

        if($cache && ($cached = Cache::instance()->get(self::cache_key($num))))
            return $cached;

        try {
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_URL, self::NEWSURL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s 
            $news = curl_exec($ch); 
            curl_close($ch);
            
            $news = json_decode($news);
        } catch(Exception $e) {
            $news = false;
        }
    
        if($news && $cache)
            Cache::instance()->set(self::cache_key($num), $news);
        return $news;
    }

    public static function fetchindex($cache=true){
        if($cache && ($cached = Cache::instance()->get("blog-index")))
            return $cached;

        try {
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_URL, self::PAGEURL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s 
            $index = curl_exec($ch); 
            curl_close($ch);
            
            $pages = array();
            $index = json_decode($index);
            foreach($index->pages as $page){
                $pages[] = $page; 
            }
        } catch(Exception $e) {
            $pages = false;
        }

        if($pages && $cache)
            Cache::instance()->set("blog-index", $pages);
        return $pages;
    }

}
