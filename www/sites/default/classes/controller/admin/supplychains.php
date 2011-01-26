<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Controller_Admin_Supplychains extends Sourcemap_Controller_Layout {

    const PAGESZ_MIN = 1;
    const PAGESZ_MAX = 25;

    public $layout = 'admin';
    public $template = 'admin/supplychains';

    public function action_index() {
        $limit = isset($_GET['l']) ? 
            max(self::PAGESZ_MIN, min(self::PAGESZ_MAX, (int)$_GET['l'])) : 
            self::PAGESZ_MAX;
        $offset = isset($_GET['o']) ? (int)$_GET['o'] : 0;
        $cache_key = sprintf("supplychains-%d-%d", $offset, $limit);
        if($supplychains = Cache::instance()->get($cache_key)) {
            $supplychains = unserialize($supplychains);
        } else {
            $supplychains = ORM::factory('supplychain')
                ->offset($offset)->limit($limit)
                ->find_all()->as_array('id', array('id', 'created', 'user_id'));
            Cache::instance()->set($cache_key, serialize($supplychains));
        }
        Breadcrumbs::instance()->add('Management', 'admin/')
            ->add('Supply Chains', 'admin/supplychains');
        $this->template->list = $supplychains;
        $this->template->offset = $offset;
        $this->template->limit = $limit;
        $this->template->total = ORM::factory('supplychain')->count_all();
    }
}
