<?php
class Blognews {

    const BASEURL = 'http://blog.sourcemap.com/api/get_recent_posts/';

    public static function cache_key($num) {
        return sprintf('blog-news-num-%02d', $num);
    }


    public static function fetch($num=20, $cache=true) {
        $num = max(0, min(50, $num));

        if($cache && ($cached = Cache::instance()->get(self::cache_key($num))))
            return $cached;

        try {
            $news = file_get_contents(self::BASEURL);
            $news = json_decode($news);
        } catch(Exception $e) {
            $news = false;
        }
    
        if($news && $cache)
            Cache::instance()->set(self::cache_key($num), $news);
        return $news;
    }
}
