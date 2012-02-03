<?php
/* Copyright (C) Sourcemap 2011 */

// Wraps our wordpress pages


class Controller_info extends Sourcemap_Controller_Layout {

    const BLOGURL = "http://blog.sourcemap.com/api/get_page/?id=";
    
    public $layout = 'base';
    public $template = 'wp';
    public $ssl_actions = array('secure');

    public static function cache_key($id) {
        return sprintf('blog-page-%s', $id);
    }
 
    public function action_index($slug="blog", $childSlug=null) {


        $this->layout->scripts = array(
            'sourcemap-document'
        );

        $contents = self::getContents($slug, $childSlug);

        if($contents){
            $this->template->title = $contents->page->title;
            $this->template->content = $contents->page->content;
        }
        $this->layout->page_title = 'Sourcemap: ' . $contents->page->title;
    }
    
    public function action_secure($slug="blog", $childSlug=null) {

        $this->layout->scripts = array(
            'sourcemap-document'
        );
        
        $contents = self::getContents($slug, $childSlug);

        if($contents){
            $this->template->title = $contents->page->title;
            $this->template->content = $contents->page->content;
        }
        $this->layout->page_title = 'Sourcemap: ' . $contents->page->title;
    }

    public function getContents($slug, $childSlug=null){

        // Convert slug to ID.  This is necessary because WP JSON API doesn't support heirarchical slugs 
        $id = self::slug2id($slug, $childSlug);

        // Return cached results if possible
        if($cached = Cache::instance()->get(self::cache_key($id))){
            $contents = $cached;
        }
        
        // Otherwise grab JSON from Wordpress API plugin and write a new cache
        else{
            try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, self::BLOGURL . $id);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
                $contents = curl_exec($ch);
                curl_close($ch);
                $contents = json_decode($contents);
                Cache::instance()->set(self::cache_key($id), $contents);
            } catch(Exception $e) {
                $contents = false;
            }
        }
        return $contents;
    }

    public function slug2id($slug, $childSlug=null){
        $index = Blognews::fetchindex();
        foreach ($index as $page){
            if ($page->slug == $slug){

                // Find child ID, if applicable 
                if ($childSlug){
                    foreach ($page->children as $child){
                        if ($child->slug == $childSlug)
                            return $child->id;
                    }
                }

                // Otherwise, return parent ID
                return $page->id;
            }
        }
    }

}
