<?php
class Controller_Services_Favorites extends Sourcemap_Controller_Service {

    const FAIL = 'fail';
    const EXISTS = 'exists';
    const FORBIDDEN = 'forbidden';

    public function action_get() {
        if($user = Auth::instance()->get_user()) {
            $this->response = $user->favorites->find_all()->as_array(null, true);
        } else return $this->_forbidden('You\'re not signed in.');
    }

    public function action_post() {
        if(!Auth::instance()->get_user()) {
            return $this->_forbidden('You\'re not signed in.');
        } 
        $posted = $this->request->posted_data;
        
        if(is_int(intval($posted->supplychain_id))) $posted = array($posted->supplychain_id);
        elseif(is_array($posted->supplychain_id)) $posted = $posted->supplychain_id;
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
                        if(Sourcemap_Search_Index::should_index($sc->id)) {
                            Sourcemap_Search_Index::update($sc->id);
                        }
                        continue;
                    } else $msg = self::FAIL;
                } else $msg = self::EXISTS;
            } else $msg = self::FORBIDDEN;
            $rejected[] = array($sc->id, $msg);
        }
        $this->response = array('added' => $added, 'rejected' => $rejected);
    }

    public function action_delete($scid) {
        if(!Auth::instance()->get_user()) {
            return $this->_forbidden('You\'re not signed in.');
        } 
        $user = Auth::instance()->get_user();
        $sc = ORM::factory('supplychain', $scid);
        if($sc->loaded()) {
            $user->remove('favorites', $sc);
            if(Sourcemap_Search_Index::should_index($scid)) {
                Sourcemap_Search_Index::update($scid);
            }
        } else return $this->_bad_request('Invalid supplychain id.');
        $this->response = true;
    }
}
