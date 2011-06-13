<?php
class Blognews {

    const BASEURL = 'http://sourcemap.tumblr.com/api/read/';

    public static function cache_key($num) {
        return sprintf('blog-news-num-%02d', $num);
    }


    public static function fetch($num=20, $cache=true) {
        $num = max(0, min(50, $num));

        if($cache && ($cached = Cache::instance()->get(self::cache_key($num))))
            return $cached;

        try {
            $news = file_get_contents(self::BASEURL.'?num='.$num.'&type=text&filter=text');
            $xml = new SimpleXMLElement($news);
            $news = array();
            foreach($xml->posts->post as $i => $p) {
                $news[] = (object)array(
                    'avatar' => Gravatar::avatar('leo@sourcemap.com'),
                    'title' => (string)$p->{"regular-title"},
                    'body' => (string)$p->{"regular-body"}
                );
            }
        } catch(Exception $e) {
            die($e);
            $news = false;
        }
    
        if($news && $cache)
            Cache::instance()->set(self::cache_key($num), $news);
        return $news;
    }
}
