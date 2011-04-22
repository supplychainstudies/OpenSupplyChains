<?php
class Controller_Services_Favorites extends Sourcemap_Controller_Service {

    const FAIL = 'fail';
    const EXISTS = 'exists';
    const FORBIDDEN = 'forbidden';

    public function before() {
        $pbefore = parent::before();
        if(!Auth::instance()->get_user()) {
            return $this->_forbidden('You\'re not logged in.');
        }
        return $pbefore;
    }

    public function action_get() {
        $user = Auth::instance()->get_user();
        $this->response = $user->favorites->find_all()->as_array(null, 'id');
    }

    public function action_post() {
        $posted = $this->request->posted_data;
        if(is_int($posted)) $posted = array($posted);
        elseif(is_array($posted)) $posted = $posted;
        else {
            return $this->_bad_request('Supplychain id or array of ids expected.');
        }
        $user = Auth::instance()->get_user();
        $added = array();
        $rejected = array();
        for($i=0; $i<count($posted); $i++) {
            $sc = ORM::factory('supplychain', $posted[$i]);
            $msg = false;
            if($sc->loaded() && $sc->user_can($user->id, Sourcemap::READ)) {
                if(!$user->has('favorites', $sc)) {
                    if($user->add('favorites', $sc, array('timestamp' => time()))) {
                        $added[] = $sc->id;
                        continue;
                    } else $msg = self::FAIL;
                } else $msg = self::EXISTS;
            } else $msg = self::FORBIDDEN;
            $rejected[] = array($sc->id, $msg);
        }
        $this->response = array('added' => $added, 'rejected' => $rejected);
    }

    public function action_delete($scid) {
        $user = Auth::instance()->get_user();
        $sc = ORM::factory('supplychain', $scid);
        if($sc->loaded()) {
            $user->remove('favorites', $sc);
        } else return $this->_bad_request('Invalid supplychain id.');
        $this->response = true;
    }
}
