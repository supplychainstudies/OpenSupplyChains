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

        // Return cached results if possible
        if($cached = Cache::instance()->get(self::cache_key($page))){
            $contents = $cached;
        }
        
        // Otherwise grab JSON from Wordpress API plugin and write a new cache
        else{
            try {
                $contents = file_get_contents(self::BLOGURL . $page);
                $contents = json_decode($contents);
                Cache::instance()->set(self::cache_key($page), $contents);
            } catch(Exception $e) {
                $contents = $e;
            }
        }

        if($contents){
            $this->template->title = $contents->page->title;
            $this->template->content = $contents->page->content;
        }
    }
}
