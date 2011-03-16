<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Smita Deshpande
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */



class Controller_Dashboard extends Sourcemap_Controller_Layout {

    public $layout = 'layout';
    public $template = 'dashboard';

    public function action_index() {

	$user_id = Auth::instance()->get_user();

	if($user_id) {

	    $current_user_id = $user_id->as_array();
	    
	    $supplychain= ORM::factory('supplychain')->where('user_id', '=', $current_user_id['id']);
	    
	    $supplychains =$supplychain->order_by('id', 'ASC')->find_all();
	    
	    $supplychains_array = $supplychains->as_array(null, array('id', 'created', 'modified'));	
	    
	    $attributes = array();
	    $iterator= 0;
	    foreach($supplychains as $supplychain) {
		$attributes[] = $supplychain->attributes->find_all()->as_array(null, array('id', 'supplychain_id', 'key'));	     
		$supplychains_array[$iterator]['owner'] = $supplychain->owner->username;
		$supplychains_array[$iterator]['stops'] = $supplychain->stops->count_all();
		$supplychains_array[$iterator]['hops'] = $supplychain->hops->count_all();
		$iterator++;
	    }
	    
	    $iterator = 0;
	    foreach ($attributes as $attribute) {
		if($attribute[0]['supplychain_id'] == $supplychains_array[$iterator]['id']){
		    $supplychains_array[$iterator]['key'] = $attribute[0]['key'];
		}
		$iterator++;
	    }

	    $this->template->supplychains = $supplychains_array;
	    
	    $this->layout->scripts = array(
		'sourcemap-core', 'sourcemap-template', 'sourcemap-working', 'sourcemap-social'
		);
	    $this->layout->styles = array(
		'assets/styles/style.css', 
		'assets/styles/sourcemap.less?v=2'
		);
	} else {
	    $this->request->redirect("auth/");
	}
    }
}
