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

class Controller_Sitemap extends Controller {

    public function action_index() {
        $cache_key = 'sourcemap-sitemap';
        $ttl = 60 * 60 * 24;
        if($cached = Cache::instance()->get($cache_key)) {
            $xml = $cached;
        } else {
            // Sitemap instance.
            $sitemap = new Sitemap();

            // basics
            $urls = array(
                'home' => array('', 0.9, 'daily', time()),
                'register' => array('register/', .6, 'yearly'),
                'browse' => array('browse/', 0.7, 'daily', time()),
                'login' => array('auth/login', 0.5, 'yearly'),
                'about' => array('info/', 0.7, 'monthly'),
                'api' => array('info/api', 0.7, 'monthly'),
                'contact' => array('info/contact', 0.8, 'monthly'),
            );
            
            // categories
            $cats = Sourcemap_Taxonomy::arr();
            $nms = array();
            foreach($cats as $i => $cat) {
                $slug = Sourcemap_Taxonomy::slugify($cat->name);
                $urls['browse-'.$cat->name] = array('browse/'.$slug.'/', .7);
            }

            // public maps
            $o = 0;
            $l = 100;
            while(($results = Sourcemap_Search::find(array('o' => $o, 'l' => $l))) && $results->hits_ret) {
                foreach($results->results as $i => $r) {
                    $urls['sc-'.$r->id] = array('view/'.$r->id, 0.5, 'daily', $r->modified);
                }
                $o += $l;
            }
            
            $defaults = array(
                false, 0.5, 'daily', false
            );
            foreach($urls as $k => $urld) {
                foreach($defaults as $i => $d) {
                    if(!isset($urld[$i])) $urld[$i] = $d;
                }
                list($loc,$priority, $freq, $lastmod) = $urld;
                $new_url = new Sitemap_URL();
                $new_url->set_loc(URL::site($loc, true))
                    ->set_priority($priority)
                    ->set_change_frequency($freq);
                if($lastmod) $new_url->set_last_mod($lastmod);
                $sitemap->add($new_url);
            }
            
            $xml = $sitemap->render();
            Cache::instance()->set($cache_key, $xml, $ttl);
        }

        header('Content-Type: application/xml');
        $this->response = $xml;
    	
        die($this->response);
    }
}
