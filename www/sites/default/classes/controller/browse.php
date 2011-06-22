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
        $favorited_sql = 'select f.supplychain_id, count(f.id) as favorited from user_favorite f '.
            'left join supplychain sc on (f.supplychain_id=sc.id) '.
            ($category ? 'where sc.category = :category_id and ' : 'where ').
            'sc.other_perms & :read_flag > 0 '.
            'group by supplychain_id order by favorited desc, f.supplychain_id desc '.
            'limit 3';
        $favorited_q = DB::query(Database::SELECT, $favorited_sql);
        $favorited_q->param(':read_flag', Sourcemap::READ);
        if($category) $favorited_q->param(':category_id', $category->id);
        $favorited = $favorited_q->execute();
        $this->template->favorited = Sourcemap_Search_Simple::prep_rows($favorited);

        // most discussed
        $discussed_sql = 'select dc.supplychain_id, count(dc.id) as discussed from supplychain_comment dc '.
            'left join supplychain sc on (dc.supplychain_id=sc.id) '.
            ($category ? 'where sc.category = :category_id and ' : 'where ').
            'sc.other_perms & :read_flag > 0 '.
            'group by supplychain_id order by discussed desc, dc.supplychain_id desc '.
            'limit 3';
        $discussed_q = DB::query(Database::SELECT, $discussed_sql);
        $discussed_q->param(':read_flag', Sourcemap::READ);
        if($category) $discussed_q->param(':category_id', $category->id);
        $discussed = $discussed_q->execute();
        $this->template->discussed = Sourcemap_Search_Simple::prep_rows($discussed);


        $this->template->primary = Sourcemap_Search::find($params);

        $recent_params = $params;
        $recent_params['recent'] = 'yes';
        $recent_params['l'] = 3;
        $this->template->recent = Sourcemap_Search::find($recent_params);

    }
}
