<?php
/* Copyright (C) Sourcemap 2011 */

// Wraps our wordpress pages


class Controller_WP extends Sourcemap_Controller_Layout {

    const BLOGURL = "http://blog.sourcemap.com/api/get_page/?slug=";
    
    public $layout = 'base';
    public $template = 'wp';
   
    public static function cache_key($page) {
        return sprintf('blog-page-%s', $page);
    }
 
    public function action_index($page="blog") {
        $this->layout->scripts = array(
            'sourcemap-document'
        );

        // Return cached results if possible
        if($cached = Cache::instance()->get(self::cache_key($page))){
            $contents = $cached;
        }
        
        // Otherwise grab JSON from Wordpress API plugin and write a new cache
        else{
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, self::BLOGURL . $page);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
                $contents = curl_exec($ch);
                curl_close($ch);
                $contents = json_decode($contents);
                Cache::instance()->set(self::cache_key($page), $contents);
            } catch(Exception $e) {
                $contents = false;
            }
        }

        if($contents){
            $this->template->title = $contents->page->title;
            $this->template->content = $contents->page->content;
        }
    }
}
