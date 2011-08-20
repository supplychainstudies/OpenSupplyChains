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
        
        $this->layout->page_title = 'Browsing maps on Sourcemap';

        $cats = Sourcemap_Taxonomy::arr();
        $nms = array();
        foreach($cats as $i => $cat) {
            $nms[Sourcemap_Taxonomy::slugify($cat->name)] = $cat;
        }

        $this->template->taxonomy = Sourcemap_Taxonomy::load_tree();


        $defaults = array(
            'q' => false,
            'p' => 1,
            'l' => 20
        );

        $params = $_GET;
        if(strtolower(Request::$method) == 'post')
            $params = $_POST;

        $params = array_merge($defaults, $params);

        $params['recent'] = 'yes';
        $params['l'] = 20;

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
        $r = Sourcemap_Search::find($params);
        $p = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string',
                'key' => 'p'
            ),
            'total_items' => $r->hits_tot,
            'items_per_page' => $r->limit,
            'view' => 'pagination/basic'
        ));
        $this->template->primary = $r;
        $this->template->pager = $p;

		$params['l'] = 1;
        $this->template->favorited = Sourcemap_Search_Simple::find($params+array('favorited' => 'yes'));
        $this->template->discussed = Sourcemap_Search_Simple::find($params+array('comments' => 'yes'));
        $this->template->interesting = Sourcemap_Search_Simple::find($params+array('favorited' => 'yes','comments' => 'yes'));
        $this->template->recent = Sourcemap_Search_Simple::find($params+array('recent' => 'yes'));
    }
}
