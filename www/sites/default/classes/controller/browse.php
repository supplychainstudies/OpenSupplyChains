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
    
    public function action_index($category=false) {
        $this->layout->scripts = array(
            'sourcemap-core',
        );
        
        $this->layout->page_title = 'Browse Sourcemaps';

        $cats = Sourcemap_Taxonomy::arr();
        $nms = array();
        foreach($cats as $i => $cat) {
            $nms[Sourcemap_Taxonomy::slugify($cat->name)] = $cat;
        }

        $this->template->taxonomy = Sourcemap_Taxonomy::load_tree();


        $params = array('l' => 12);
        if($category && isset($nms[$category])) {
            $slug = $category;
            $category = $nms[$category];
            $this->template->category = $category;
            $params['c'] = $category->title;
            $this->layout->page_title .= ' - '.$category->title;
        } elseif($category) {
            Message::instance()->set('"'.$category.'" is not a valid category slug.');
            return $this->request->redirect('browse');
        } else {
            $this->template->category = false;
        }
        
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
        $this->template->interesting = Sourcemap_Search_Simple::find($iparams);

        $p = Sourcemap_Search::find($params);
        $this->template->primary = $p;

        $recent_params = $params;
        $recent_params['recent'] = 'yes';
        $recent_params['l'] = 3;
        $this->template->recent = Sourcemap_Search::find($recent_params);

    }
}
