<?php
/**
 * Description Create a Map page
 * @package    Sourcemap
 * @author     Alex Ose 
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */

class Controller_Create extends Sourcemap_Controller_Layout {

    public $layout = 'base';
    public $template = 'create';
    
    public function action_index() {

        $f = Sourcemap_Form::load('/create');
        $f->action('create')->method('post');


        if(!Auth::instance()->get_user()) {
            $this->request->redirect('auth');
        }
        
        $this->layout->scripts = array(
            'sourcemap-core', 'sourcemap-template'
        );

        $this->template->create_form = $f;

        if(strtolower(Request::$method) === 'post') {
            if($f->validate($_POST)) {
                // create!
                $p = $f->values();
                $title = $p['title'];
                $teaser = substr($p['description'], 0, 80);
                $tags = Sourcemap_Tags::join(Sourcemap_Tags::parse($p['tags']));
                $category = $p['category'];
                $public = isset($_POST['public']) ? Sourcemap::READ : 0;
                $raw_sc = new stdClass();
                if($category) $raw_sc->category = $category;
                $raw_sc->attributes = new stdClass();
                $raw_sc->attributes->title = $title;
                $raw_sc->attributes->teaser = $teaser;
                $raw_sc->attributes->tags = $tags;
                $raw_sc->stops = array();
                $raw_sc->hops = array();
                $raw_sc->user_id = Auth::instance()->get_user()->id;
                $raw_sc->other_perms = 0;
                if($public)
                    $raw_sc->other_perms |= $public;
                else
                    $raw_sc->other_perms &= ~Sourcemap::READ;
                try {
                    $new_scid = ORM::factory('supplychain')->save_raw_supplychain($raw_sc);
                    return $this->request->redirect('map/view/'.$new_scid);
                } catch(Exception $e) {
                    $this->request->status = 500;
                    Message::instance()->set('Couldn\t create your supplychain. Please contact support.');
                }
            } else {
                Message::instance()->set('Correct the errors below.');
            }
        }
    }
}
