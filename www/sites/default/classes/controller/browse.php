<?php
/**
 * Description Browse 
 * @package    Sourcemap
 * @author     Alex Ose 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */

class Controller_Browse extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'browse';
    
    public function action_index() {
        $this->layout->scripts = array(
            'sourcemap-core',
        );
        
        $this->layout->page_title = 'Browse Sourcemaps';
        $supplychain_rows = ORM::factory('supplychain')
            ->where(DB::expr('other_perms & '.Sourcemap::READ), '>', 0)
            ->limit(12)->order_by('created', 'desc')
            ->find_all()->as_array('id', true);
        foreach($supplychain_rows as $i => $sc) {
            $ks =  ORM::factory('supplychain')->kitchen_sink($sc->id);
            $ks->owner = ORM::factory('user', $sc->user_id)->find();
            $supplychains[] = $ks;
        }
        $this->template->supplychains = $supplychains;
    }
}
