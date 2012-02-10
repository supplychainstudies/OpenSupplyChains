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

class Sourcemap_Taxonomy {

    public $r = 0;
    public $l =  1;
    public $data = null;
    public $parent = null;
    public $children = null;

    public static function slugify($nm) {
        return preg_replace('/[^a-z_-]+/', '-', strtolower($nm));
    }

    public static function arr($k=null, $vs=true) {
        $cats = ORM::factory('category')->find_all();
        return $cats->as_array($k, $vs);
    }

    public static function arr2tree($arr) {
        // assumed to be sorted by left asc
        $minlt = 0;
        $maxrt = $arr ? $arr[count($arr)-1]->right : 0;
        $maxrt += 1;
        $t = new Sourcemap_Taxonomy($minlt, $maxrt, (object)array('title' => 'Categories'));
        $cur = $t;
        while($nxt = array_shift($arr)) {
            $nxt = new Sourcemap_Taxonomy($nxt->left, $nxt->right, $nxt);
            while(($nxt->r > $cur->r) && $cur->parent) {
                $cur = $cur->parent;
            }
            $cur->graft($nxt);
            $cur = $nxt;
        }
        return $t;
    }

    public static function load_tree($root_id=null) {
        $catmod = ORM::factory('category');
        if($root_id) {
            $root = ORM::factory('category', $root_id);
            if(!$root->loaded()) 
                throw new Exception('Invalid root "'.$root_id.'".');
            $catmod->where('left', '>', $root->left);
            $catmod->and_where('right', '<', $root->right);
        }
        $tree = $catmod->order_by('left')->find_all()->as_array(null, true);
        return self::arr2tree($tree);
    }

    public static function flatten($tree=null, $d=0) {
        if($tree === null) $tree = self::load_tree();
            $flat = array();
            if (isset($tree->data->id)){
                $flat[] = array($tree->data->id, $tree->data->name, $tree->data->title, $d);
                foreach($tree->children as $ci => $ch) {
                    $flat = array_merge($flat, self::flatten($ch, $d+1));
                }
            }
        return $flat;
    }

    public static function load_ancestors($cat_id) {
        $cat = ORM::factory('category', $cat_id);
        $a = false;
        if($cat->loaded()) {
            $m = ORM::factory('category');
            $a = $m->where('left', '<=', $cat->left)
                ->and_where('right', '>=', $cat->right)
                ->order_by('left', 'asc')
                ->find_all()->as_array(null, true);
        }
        return $a;
    }

    public static function load_children($cat_id) {
        $cat = ORM::factory('category', $cat_id);
        $ch = false;
        if($cat->loaded()) {
            $m = ORM::factory('category');
            $ch = $m->where('left', '>=', $cat->left)
                ->and_where('right', '<=', $cat->right)
                ->order_by('left', 'asc')
                ->find_all()->as_array(null, true);
        }
        return $ch;
    }

    public function __construct($l, $r, $d=null) {
        $this->l = $l;
        $this->r = $r;
        $this->data = $d;
        $this->children = array();
    }

    public function graft($t) {
        $t->parent = $this;
        $this->children[] = $t;
        return $this;
    }
}
