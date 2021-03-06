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

class Controller_Admin_Supplychains extends Controller_Admin { 
 

    public $layout = 'admin';
    public $template = 'admin/supplychains/list';
  

    public function action_index() {    
    
        $supplychain = ORM::factory('supplychain');
        $page = max($this->request->param('page'), 1);
        $items = 20;
        $offset = ($items * ($page - 1));
        $count = $supplychain->count_all();
        $pagination = Pagination::factory(
        array('current_page' => array('source' => 'query_string', 'key' => 'page'),
              'total_items' => $supplychain->count_all(),
              'items_per_page' => $items,
            ));

        $supplychains = $supplychain->order_by('modified', 'DESC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all();        
        $supplychains_array = $supplychains->as_array('id', array('id', 'created')); 
        
        $attributes = array();
        foreach($supplychains as $supplychain) {
            $scid = $supplychain->id;
            $supplychains_array[$scid] = (array)$supplychains_array[$scid];
            $supplychains_array[$scid]['owner'] = $supplychain->owner->username;
            $supplychains_array[$scid]['created'] = date("F j, Y, g:i a", $supplychains_array[$scid]['created']);
            $supplychains_array[$scid]['attributes'] = $supplychain->attributes->find_all()->as_array('key', 'value');
        }

        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        $this->template->list = $supplychains_array;

        Breadcrumbs::instance()->add('Management', 'admin/')
        ->add('Supply Chains', 'admin/supplychains');
    }

    /*
     * Lists all the details associated with supplychain_id
     */
    public function action_details($id) {

        $this->template = View::factory('admin/supplychains/details');
        
        $supplychain = ORM::factory('supplychain', $id);
        
        $stop_count = $supplychain->stops->count_all();
        $hop_count = $supplychain->hops->count_all();
        
        $supplychain_permissions = $supplychain->other_perms;
        $supplychain_permissions == 1 ? $permissions = "public" : $permissions = "private";
        $permissions_array = array("public", "private");
        $group_permissions_array = array("Nothing", "Read", "Write", "Read and Write");
        
        // TODO: fix this (masks)
        switch($supplychain->usergroup_perms) {
            case 0:
                $usergroup_perms = "Nothing";
                break;
            case 1:
                $usergroup_perms = "Read";
                break;
            case 2:
                $usergroup_perms = "Write";
                break;
            case 3:
                $usergroup_perms = "Read and Write";
                break;
            default:
                
        }
        
        
        $attributes= $supplychain->attributes->find_all()->as_array('key', 'value');
        
        $alias = $supplychain->alias->find_all()->as_array(null, array('supplychain_id', 'site', 'alias'));
        
        $owner_group = $supplychain->owner_group->find()->as_array(null, array('id', 'name'));
        $owner = $supplychain->owner->as_array(null, 'username');
        
        
        $this->template->stop_count = $stop_count;
        $this->template->hop_count = $hop_count;
        $this->template->attributes = $attributes;
        $this->template->alias = $alias;
        $this->template->permissions = $permissions;
        $this->template->permissions_array = $permissions_array;
        $this->template->id = $id;
        $this->template->owner_group = $owner_group['name'];
        $this->template->owner = $owner['username'];
        $this->template->owner_id = $owner['id'];
        $this->template->usergroup_perms = $usergroup_perms;
        $this->template->group_permissions_array = $group_permissions_array;
        $this->template->flags = $supplychain->flags;
        
        
        //create an alias
        $post = Validate::factory($_POST);
        $post->rule('site', 'not_empty')
        ->rule('alias', 'not_empty')
        ->filter('site', 'strip_tags')
        ->filter('alias', 'strip_tags')
        ->filter(true, 'trim');
        
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            
            $site_added = $post->site;
            $alias_added = $post->alias;
            
                    
            $supplychain_alias = ORM::factory('supplychain_alias');
            $supplychain_alias->supplychain_id = $id;
            $supplychain_alias->site = $site_added;
            $supplychain_alias->alias = $alias_added;

            try {
                $supplychain_alias->save();
            } catch(Exception $e) {
                Message::instance()->set('Could not create alias. Violates the unique (site, alias)');
            }        
            
            $this->request->redirect("admin/supplychains/".$id);
        }

        Breadcrumbs::instance()->add('Management', 'admin/')
                ->add('Supply Chains', 'admin/supplychains')
                ->add(ucwords($id), 'admin/supplychains/'.$id);

    }


    public function action_delete_alias($id) {

        $post = Validate::factory($_POST);
        $post->rule('alias', 'not_empty')
        ->rule('site', 'not_empty')
        ->filter('alias', 'strip_tags')
        ->filter('site', 'strip_tags')
        ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
        $post = (object)$post->as_array();
        $alias = $post->alias;  
        $site = $post->site;  
        $supplychain_alias= ORM::factory('supplychain_alias', array('site' => $site, 'alias' => $alias, 'supplychain_id' => $id));
        
        try {
            $supplychain_alias->delete();        
        } catch (Exception $e) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        }
        
        } else {
        Message::instance()->set('Bad request.');
        }
        
        $this->request->redirect("admin/supplychains/".$id);
    }

    public function action_change_perms($id) {
    
        $post = Validate::factory($_POST);
        $post->rule('perms', 'not_empty')
        ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
        $post = (object)$post->as_array();
        $supplychain = ORM::factory('supplychain', $id);
        $post->perms == "public" ? $perms=1 : $perms=0;
        $supplychain->other_perms = $perms;
        try {
            $supplychain->save();
            Message::instance()->set('Supplychain permissions changed!', Message::SUCCESS);
        } catch (Exception $e) {
            Message::instance()->set('Permissions not changed, please try again.');
        }
        $this->request->redirect("admin/supplychains/".$id);

        }
    }

    public function action_chown($id) {
        if(strtolower(Request::$method) == 'post') {
            $post = $_POST;
            $sc = ORM::factory('supplychain', $id);
            if($sc->loaded()) {
                if(isset($post["chown"])) {
                    if(isset($post["new_owner"]) && $post["new_owner"]) {
                        if(is_numeric($post["new_owner"])) {
                            $new_owner = ORM::factory('user', $post["new_owner"]);
                        } elseif(is_string($post["new_owner"])) {
                            $new_owner = ORM::factory('user')->where('username', '=', $post["new_owner"])
                                ->find();
                        } else $new_owner = false;
                        if($new_owner && $new_owner->loaded()) {
                            $sc->user_id = $new_owner->id;
                            try {
                                $sc->save();
                                Message::instance()->set('Changed owner to "'.$post["new_owner"].'".');
                            } catch(Exception $e) {
                                Message::instance()->set('Could not update owner.');
                            }
                        } else {
                            Message::instance()->set('Invalid user.');
                        }
                    }
                }
            } else {
                Message::instance()->set('Invalid supplychain.');
            }
        }
        $this->request->redirect('admin/supplychains/'.$id);
    }
    
    public function action_delete_supplychain($id) {
    
        $supplychain = ORM::factory('supplychain', $id);
    
        try {
        $supplychain->delete();        
        $this->request->redirect("admin/supplychains/");
        } catch (Exception $e) {
        Message::instance()->set('Could not delete the supplychain.');
        }
    }

    public function action_refresh_supplychain($id) {
    
        $supplychain = ORM::factory('supplychain', $id);

        if($supplychain->loaded()) {
            // pass
        } else {
            Message::instance()->set('Invalid supplychain.');
            $this->request->redirect('admin/supplychains/');
        }
   
        try {
            // Delete and recreate cache entry
            Cache::instance()->delete('supplychain-'.$id);
            Message::instance()->set('Primary cache entry for supplychain '.$id.' deleted.', Message::INFO);

            if(Sourcemap_Search_Index::should_index($id)) {
                Message::instance()->set('Supplychain '.$id.' re-indexed.', Message::INFO);
                Sourcemap_Search_Index::update($id);
            } else {
                Message::instance()->set('Supplychain '.$id.' de-indexed.', Message::INFO);
                Sourcemap_Search_Index::delete($id);
            }
           
            // Recreate static image
            $szs = Sourcemap_Map_Static::$image_sizes;
            foreach($szs as $snm => $sz) {
                $ckey = Sourcemap_Map_Static::cache_key($id, $snm);
                Cache::instance()->delete($ckey);
            }
            Message::instance()->set('Removed cached static map images for map '.$id.'.', Message::INFO);

            Sourcemap::enqueue(Sourcemap_Job::STATICMAPGEN, array(
                'baseurl' => Kohana_URL::site('/', true),
                'environment' => Sourcemap::$env,
                'supplychain_id' => (int)$id,
                'sizes' => Sourcemap_Map_Static::$image_sizes,
                'thumbs' => Sourcemap_Map_Static::$image_thumbs
            ));
            Message::instance()->set('Queued job for new static maps. Should regenerate within 30-60 seconds.', Message::INFO);

            // I don't know that lotus, et al. want to update
            // the modified time. I think we just want to trigger
            // a reload/redraw for cache and static maps, respectively.
            //$sc = ORM::factory('supplychain', $id);
            //$sc->modified = time();
            //$sc->save();

            $this->request->redirect("admin/supplychains/$id");
        } catch (Exception $e) {
            Message::instance()->set('Could not refresh supplychain '.$id.'.');
        }
    }

    public function action_change_usergroup_perms($id) {

        $post = Validate::factory($_POST);
        $post->rule('groupperm', 'not_empty')
            ->filter(true, 'trim');
            if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            $supplychain = ORM::factory('supplychain', $id);
            switch($post->groupperm) {
            case "Nothing":
                $usergroup_perms = 0;
                break;
            case "Read":
                $usergroup_perms = 1;
                break;
            case "Write":
                $usergroup_perms = 2;
                break;
            case "Read and Write":
                $usergroup_perms = 3;
            }
            try {
                $supplychain->usergroup_perms = $usergroup_perms;
                $supplychain->save();
                Message::instance()->set('Group permissions changed!.');
            } catch (Exception $e) {
                Message::instance()->set('Permissions not changed, please try again.');
            }
        }
        $this->request->redirect("admin/supplychains/".$id);

    }
}


