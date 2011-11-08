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

class Controller_Browse extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'browse';
    
    public function action_index($category=false) {
        $this->layout->scripts = array(
            'sourcemap-core',
        );
        
        $this->layout->styles = $this->default_styles;
        $this->layout->styles[] = 'sites/default/assets/styles/carousel.less';

        $this->layout->page_title = 'Browse maps by Category';

        $cats = Sourcemap_Taxonomy::arr();
        
        $nms = array();
        $defaults = array(
            'q' => false,
            'p' => 1,
            'l' => 999 
        );

        foreach($cats as $i => $cat) {
            $nms[Sourcemap_Taxonomy::slugify($cat->name)] = $cat;
        }

        $this->template->taxonomy = Sourcemap_Taxonomy::load_tree();

        $params = $_GET;
        if(strtolower(Request::$method) == 'post')
            $params = $_POST;

        $params = array_merge($defaults, $params);

        // if a specific category is set, use the category view 
        if($category && isset($nms[$category])) {
            $slug = $category;
            $category = $nms[$category];
            $params['c'] = $category->name;
            $this->layout->page_title .= ' - '.$category->title;
            $this->template->category = $category->title;
            $this->template->category_name = $category->name;
            $searches = Sourcemap_Search::find($params+array('recent' => 'yes', 'l' => '50'));
            
            $p = Pagination::factory(array(
                'current_page' => array(
                    'source' => 'query_string',
                    'key' => 'p'
                ),
                'total_items' => $searches->hits_tot,
                'items_per_page' => $searches->limit,
                'view' => 'pagination/basic'
            ));

            $this->template->pager = $p;
 
        } elseif($category) {
            if ($category == "uncategorized"){
                $this->layout->page_title .= ' - Uncategorized';
                $this->template->category = 'Uncategorized';
                $this->template->category_name = 'uncategorized';
                $searches = Sourcemap_Search::find($params+array('c' => '', 'l' => '50'));
            
                $p = Pagination::factory(array(
                    'current_page' => array(
                        'source' => 'query_string',
                        'key' => 'p'
                    ),
                    'total_items' => $searches->hits_tot,
                    'items_per_page' => $searches->limit,
                    'view' => 'pagination/basic'
                ));

            $this->template->pager = $p;
            }
            else{
                Message::instance()->set('"'.$category.'" is not a valid category.');
                return $this->request->redirect('browse');
            }
        } else {
        
            // Top-level category view
            $this->template->category = false;
            
            // Create an array of all top-level categories
            $toplevels = array();
            $tree = Sourcemap_Taxonomy::load_tree();
            foreach($tree->children as $subtree){
                array_push($toplevels, $subtree->data->name);
            }
           
            // Do a general search for every top-level category
            $cache_key = 'sourcemap-browse-searches';
            $ttl = 3600;
            if($cached = Cache::instance()->get($cache_key)) {
                $searches = $cached;
            } else {
                $searches = array();
                foreach ($toplevels as $i => $cat){
                    $params['c'] = $cat;
                    $search = Sourcemap_Search::find($params+array('recent' => 'yes', 'limit' => '999'));
                    $search->cat_title = $nms[$cat]->title; 
                    array_push($searches, $search);
                }
                // Sort array by number of result
                function sort_searches($a, $b){
                    return $b->hits_tot - $a->hits_tot;
                }
                usort($searches, "sort_searches");
                Cache::instance()->set($cache_key, $searches, $ttl);
            }
        }
        
        $this->template->searches = $searches;

    	// Other searches
        $cache_key = 'sourcemap-browse-alternates';
        $ttl = 60;
        if($cached = Cache::instance()->get($cache_key)) {
            $alternates = $cached;
        } else {
            $alternates = array();
            $alternates['favorited'] = Sourcemap_Search_Simple::find($params+array('favorited' => 'yes'));
            $alternates['interesting'] = Sourcemap_Search::find(array('recent' => 'yes', 'l' => 3));
            $alternates['uncategorized'] = Sourcemap_Search::find(array('c' => '', 'l' => 999));
            Cache::instance()->set($cache_key, $alternates, $ttl);
        }

        $this->template->favorited = $alternates['favorited'];     
        $this->template->interesting = $alternates['interesting'];  
        $this->template->uncategorized = $alternates['uncategorized'];
    }
}
