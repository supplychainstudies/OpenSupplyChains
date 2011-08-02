<?php
class Controller_Search extends Sourcemap_Controller_Layout {
    public $template = 'search';

    public function action_index() {

        $this->layout->scripts = array(
            'sourcemap-core'
        );
        
        $q = "NOAP";

        if (isset($_GET['q'])){
            $q = $_GET['q'];
        }

        elseif (isset($_POST['q'])){
            $q = $_POST['q'];
        }


        $r = Sourcemap_Search::simple($q);

        $this->template->search_result = $r;

        $p = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string',
                'key' => 'p'
            ),
            'total_items' => $r->hits_tot,
            'items_per_page' => $r->limit,
            'view' => 'pagination/basic'
        ));

        $this->template->pager = $p;
    }
}
