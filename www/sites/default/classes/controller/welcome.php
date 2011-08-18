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
        $this->layout->styles[] = 'sites/default/assets/styles/slider.less';
        
        $this->layout->page_title = 'Sourcemap: The Crowdsourced Directory of Product Supply Chains and Carbon Footprints';
        $recent = Sourcemap_Search::find(array('recent' => 'yes', 'l' => 4));
        $popular = Sourcemap_Search::find(array('comments' => 'yes', 'favorited' => 'yes', 'l' => 4));
        $featured = Sourcemap_Search::find(array('featured' => 'yes', 'l' => 4));
        $morefeatured = Sourcemap_Search::find(array('featured' => 'yes', 'l' => 2, 'o' => 0));

        $this->template->recent = $recent->results;
        $this->template->popular = $popular->results;
        $this->template->featured = $featured->results;
        $this->template->morefeatured = $morefeatured->results;

        $this->template->news = Blognews::fetch(4);
    }
}
