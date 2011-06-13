<?php
/**
 * Description Home Page 
 * @package    Sourcemap
 * @author     Alex Ose 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */

class Controller_Welcome extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'welcome';
    
    public function action_index() {
        $this->layout->scripts = array(
            'sourcemap-core',
            'sourcemap-welcome'
        );

        $this->layout->styles = $this->default_styles;
        $this->layout->styles[] = 'sites/default/assets/styles/slider.css';
        
        $this->layout->page_title = 'Welcome to Sourcemap.';
        $supplychain_rows = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(12)->order_by('created', 'desc')
            ->find_all()->as_array('id', true);
        $supplychains = array();
        foreach($supplychain_rows as $i => $sc) {
            $ks =  ORM::factory('supplychain')->kitchen_sink($sc->id);
            $ks->owner = ORM::factory('user', $sc->user_id)->find();
            $supplychains[] = $ks;
        }
        $this->template->news = Blognews::fetch(4);
        $this->template->supplychains = $supplychains;
    }
}
