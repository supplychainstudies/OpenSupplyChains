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
        $recent = Sourcemap_Search::find(array('recent' => 'yes', 'l' => 12));
        $this->template->recent = $recent->results;

        $featured = Sourcemap_Search::find(array('featured' => 'yes', 'l' => 12));
        $this->template->featured = $featured;

        $params = array('l' => 12);

        // most favorited
        $fparams = $params;
        $fparams['favorited'] = 'yes';
        $this->template->favorited = Sourcemap_Search_Simple::find($fparams);

        // most discussed
        $dparams = $params;
        $dparams['comments'] = 'yes';
        $this->template->discussed = Sourcemap_Search_Simple::find($dparams);

        // most interesting
        $iparams = $params;
        $iparams['comments'] = 'yes';
        $iparams['favorited'] = 'yes';
        $this->template->popular = Sourcemap_Search_Simple::find($iparams);

        $this->template->news = Blognews::fetch(4);
        $this->template->supplychains = $recent;
    }
}
