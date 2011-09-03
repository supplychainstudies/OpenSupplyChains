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

class Controller_Admin_Aliases extends Controller_Admin {

    public $layout = 'admin';
    public $template = 'admin/aliases/list';


    public function action_index() {

        $supplychain_alias = ORM::factory('supplychain_alias');
        $page = max($this->request->param('page'), 1);
        $items = 20;
        $offset = ($items * ($page - 1));
        $count = $supplychain_alias->count_all();
        $pagination = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string', 
                'key' => 'page'
            ),
            'total_items' => $supplychain_alias->count_all(),
            'items_per_page' => $items,
        ));
        $this->template->supplychain_alias = $supplychain_alias->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()->as_array(null, array('id', 'site', 'alias', 'supplychain_id'));
        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        
        $supplychain_alias_count = $supplychain_alias->count_all();
        
        $post = Validate::factory($_POST);
        $post->rule('site', 'not_empty')
        ->rule('alias', 'not_empty')
        ->filter('site', 'strip_tags')
        ->filter('alias', 'strip_tags')
        ->rule('supplychain_id', 'not_empty')
        ->filter(true, 'trim');
        
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $check = false;
            $post = (object)$post->as_array();
            
            $site_added = $post->site;
            $alias_added = $post->alias;
            $id = $post->supplychain_id;
                    
            // check if the alias already exists, if not add new alias
            
            $supplychain_alias = ORM::factory('supplychain_alias');
            $supplychain_alias->supplychain_id = $id;
            $supplychain_alias->site = $site_added;
            $supplychain_alias->alias = $alias_added;
            try {
                $supplychain_alias->save();
            } catch(Exception $e) {
                Message::instance()->set('Could not create alias. Violates the unique (site, alias)');
            }
            $this->request->redirect('admin/aliases');
        
        }
    
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Aliases', 'admin/aliases');
    } 

    public function action_delete_supplychain_alias($id) {
        $supplychain_alias = ORM::factory('supplychain_alias', $id);
        $supplychain_alias->delete();
        $this->request->redirect("admin/aliases/");
    }
}
