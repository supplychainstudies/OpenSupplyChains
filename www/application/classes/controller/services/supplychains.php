<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
 class Controller_Services_Supplychains extends Sourcemap_Controller_Service {
    public function action_get() {
        $id = $this->request->param('id', false);
        if($id) {
            $supplychain = ORM::factory('supplychain', $id);
            if(!$supplychain->loaded()) {
                return $this->_not_found('Supplychain not found.');
            }
            $this->response = array(
                'supplychain' => $supplychain->kitchen_sink()
            );
        } else {
            $params = $this->_list_parameters();
            $supplychains = ORM::factory('supplychain')
                ->offset($params->offset)->limit($params->limit)
                ->find_all()->as_array('id', array('id', 'created'));
            $this->response = array(
                'supplychains' => $supplychains,
                'parameters' => $params, 
                'total' => ORM::factory('supplychain')->count_all()
            );
        }
    }
 }
