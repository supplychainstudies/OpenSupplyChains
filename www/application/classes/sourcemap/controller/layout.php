<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Sourcemap_Controller_Layout extends Controller_Template {

    public $layout = 'base';
    public $template = 'template';

    public $default_styles = array(
        'sites/default/assets/styles/reset.css',
        'assets/styles/general.less',
    );

    public function before() {
        $pret = parent::before();
        if($this->auto_render === true) {
            $this->layout = View::factory('layout/'.$this->layout);
        }
        return $pret;
    }

    public function after() {
        $pret = parent::after();
        if($this->auto_render === true) {
            if(!isset($this->layout->styles) || !$this->layout->styles) 
                $this->layout->styles = $this->default_styles;
            $this->layout->content = $this->request->response;
            $this->request->response = $this->layout;
        }
        return $pret;
    }

    public function _forbidden($msg='Forbidden') {
        $this->template = View::factory('error');
        $this->template->error_message = $msg;
        return;
    }
}
