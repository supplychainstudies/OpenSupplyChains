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
        $this->layout->page_title = 'Browse maps by Category';

        $cats = Sourcemap_Taxonomy::arr();
        
        $nms = array();
        $defaults = array(
            'q' => false,
            'p' => 1,
            'l' => 20
        );

        foreach($cats as $i => $cat) {
            $nms[Sourcemap_Taxonomy::slugify($cat->name)] = $cat;
        }

        $this->template->taxonomy = Sourcemap_Taxonomy::load_tree();

        $params = $_GET;
        if(strtolower(Request::$method) == 'post')
            $params = $_POST;

        $params = array_merge($defaults, $params);

        if($category && isset($nms[$category])) {
            // if a specific category is set, use the category view 
            $slug = $category;
            $category = $nms[$category];
            $params['c'] = $category->name;
            $this->layout->page_title .= ' - '.$category->title;
            $this->template->category = $category->title;
            $searches = Sourcemap_Search::find($params+array('recent' => 'yes'));
        } elseif($category) {
            Message::instance()->set('"'.$category.'" is not a valid category.');
            return $this->request->redirect('browse');
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
            $searches = array();
            foreach ($toplevels as $i => $cat){
                $params['c'] = $cat;
                $search = Sourcemap_Search::find($params+array('recent' => 'yes'));
                array_push($searches, $search);
            }

            // Sort array by number of result
            function sort_searches($a, $b){
                return count($b->results) - count($a->results);
            }
            usort($searches, "sort_searches");
        }
        
        $this->template->searches = $searches;

    	$params['l'] = 1;
        $this->template->favorited = Sourcemap_Search_Simple::find($params+array('favorited' => 'yes'));
        $this->template->discussed = Sourcemap_Search_Simple::find($params+array('comments' => 'yes'));
        $this->template->interesting = Sourcemap_Search_Simple::find($params+array('comments' => 'yes'));
        $this->template->recent = Sourcemap_Search_Simple::find($params+array('recent' => 'yes'));
        
    }

<<<<<<< HEAD
=======

>>>>>>> master
}


