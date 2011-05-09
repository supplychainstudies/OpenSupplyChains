<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */

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

        $supplychains = $supplychain->order_by('modified', 'ASC')
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
        $supplychains_array[$scid]['title'] = $supplychain->attributes->find_all()->as_array(null, array('key', 'value'));
        }

        $this->template->page_links = $pagination->render();
        $this->template->offset = $pagination->offset;
        $this->template->list = $supplychains_array;

        Breadcrumbs::instance()->add('Management', 'admin/')
        ->add('Supply Chains', 'admin/supplychains');
    }

    /*
     * Lists all the details associated with supplychain_id
     * 
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
        }
        
        
        $attributes= $supplychain->attributes->find_all()->as_array(null, array('key', 'value'));
        
        $alias = $supplychain->alias->find_all()->as_array(null, array('supplychain_id', 'site', 'alias'));
        
        $owner_group = $supplychain->owner_group->find()->as_array(null, array('id', 'name'));
        $owner = $supplychain->owner->find()->as_array(null, 'username');
        
        
        $this->template->stop_count = $stop_count;
        $this->template->hop_count = $hop_count;
        $this->template->attributes = $attributes;
        $this->template->alias = $alias;
        $this->template->permissions = $permissions;
        $this->template->permissions_array = $permissions_array;
        $this->template->id = $id;
        $this->template->owner_group = $owner_group['name'];
        $this->template->owner = $owner['username'];
        $this->template->usergroup_perms = $usergroup_perms;
        $this->template->group_permissions_array = $group_permissions_array;
        
        
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
        
        Breadcrumbs::instance()->add('Management', 'admin/')
                ->add('Supply Chains', 'admin/supplychains')
                ->add(ucwords($id), 'admin/supplychains/'.$id);
        }
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
            Message::instance()->set('Supplychain permissions changed!.');
        } catch (Exception $e) {
            Message::instance()->set('Permission not changed, please try again.');
        }
        $this->request->redirect("admin/supplychains/".$id);

        }
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


