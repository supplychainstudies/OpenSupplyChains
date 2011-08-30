<?php
class Controller_Search extends Sourcemap_Controller_Layout {
    public $template = 'search';

    public function action_index() {

        $this->layout->scripts = array(
            'sourcemap-core'
        );
        
        $defaults = array(
            'q' => false,
            'p' => 1,
            'l' => 15
        );

        $params = $_GET;
        if(strtolower(Request::$method) == 'post')
            $params = $_POST;

        //$params = array_merge($defaults, $params);
        $search_params = $defaults;
        foreach($search_params as $k => $v) 
            if(isset($params[$k])) 
                $search_params[$k] = $params[$k];

        $r = Sourcemap_Search::find($search_params);

        $this->template->search_result = $r;
		$this->layout->page_title = 'Search results for ['.$search_params['q'].'] on Sourcemap';
        
        $p = Pagination::factory(array(
            'current_page' => array(
                'source' => 'q',
                'key' => 'p'
            ),
            'total_items' => $r->hits_tot,
            'items_per_page' => $r->limit,
            'view' => 'pagination/basic',
            'url_params' => $search_params
        ));

        $this->template->pager = $p;
    }
}
