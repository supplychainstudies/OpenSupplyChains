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
	
	
	$supplychains = $supplychain->order_by('id', 'ASC')
	    ->limit($pagination->items_per_page)
	    ->offset($pagination->offset)
	    ->find_all();        
	$supplychains_array = $supplychains->as_array(null, array('id', 'created')); 
        $supplychains = $supplychain->order_by('id', 'ASC')
	    ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
	    ->find_all();        
        $supplychains_array = $supplychains->as_array('id', array('id', 'created')); 
        
        $attributes = array();
        foreach($supplychains as $supplychain) {
            $scid = $supplychain->id;
            $supplychains_array[$scid] = (array)$supplychains_array[$scid];
            $supplychains_array[$scid]['owner'] = $supplychain->owner->username;
            $supplychains_array[$scid]['attributes'] = $supplychain->attributes->find_all()->as_array(null, 'key');	
        }

        
	$this->template->page_links = $pagination->render();
	$this->template->offset = $pagination->offset;
	$this->template->list = $supplychains_array;
	Message::instance()->set('Total supplychains '.$count);
	Breadcrumbs::instance()->add('Management', 'admin/')
	    ->add('Supply Chains', 'admin/supplychains');

    }


    public function action_details($id) {
        $this->template = View::factory('admin/supplychains/details');
        
        $supplychain = ORM::factory('supplychain', $id);	

        $stop_count = $supplychain->stops->count_all();
        $hop_count = $supplychain->hops->count_all();

        $attribute= $supplychain->attributes->find_all()->as_array(null, 'key');

        $alias = $supplychain->alias->find_all()->as_array(null, array('supplychain_id', 'site', 'alias'));

        $this->template->stop_count = $stop_count;
        $this->template->hop_count = $hop_count;
        $this->template->attribute_key = $attribute[0];
        $this->template->alias = $alias;


        //create an alias
        $post = Validate::factory($_POST);
        $post->rule('site', 'not_empty')
            ->rule('alias', 'not_empty')
            ->filter(true, 'trim');
        
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $check = false;
            $post = (object)$post->as_array();

            $site_added = $post->site;
            $alias_added = $post->alias;
            
            $supplychain = ORM::factory('supplychain')
            ->where('id', '=', $id)
            ->find();
        
            $alias = $supplychain->alias->find_all()->as_array(null, array('site', 'alias'));

            $alias_names = array();
            $site_names = array();
            
            // check if the alias already exists, if not add new alias
            foreach($alias as $alias_array) {	
            $alias_names[] = $alias_array['alias']; 
            $site_names[] = $alias_array['site']; 

            }
            if((!in_array($alias_added, $alias_names) && (!in_array($site_added, $site_names)))) {
            $supplychain_alias = ORM::factory('supplychain_alias');
            $supplychain_alias->supplychain_id = $id;
            $supplychain_alias->site = $site_added;
            $supplychain_alias->alias = $alias_added;
            $supplychain_alias->save();
            
            } else {
            Message::instance()->set('Alias and site already exist.');
            }
            
            $this->request->redirect("admin/supplychains/".$id);
            
        }
        Breadcrumbs::instance()->add('Management', 'admin/')
                ->add('Supply Chains', 'admin/supplychains')
                ->add(ucwords($attribute[0]), 'admin/supplychains/'.$id);
        
    }


    public function action_delete_alias($id) {
        $post = Validate::factory($_POST);
        $post->rule('alias', 'not_empty')
	    ->rule('site', 'not_empty')
            ->filter(true, 'trim');
        if(strtolower(Request::$method) === 'post' && $post->check()) {
            $post = (object)$post->as_array();
            $alias = $post->alias;  
	    $site = $post->site;  
            $supplychain_alias= ORM::factory('supplychain_alias', array('site' => $site, 'alias' => $alias, 'supplychain_id' => $id));
	    $supplychain_alias->delete();	    

        } elseif(strtolower(Request::$method === 'post')) {
            Message::instance()->set('Could not delete role.', Message::ERROR);
        } else {
            Message::instance()->set('Bad request.');
        }

        $this->request->redirect("admin/supplychains/".$id);
    }
}


