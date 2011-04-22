<?php
class Controller_Services_Comments extends Sourcemap_Controller_Service {

    const MAXLEN = 10240;

    public function before() {
        $pbefore = parent::before();
        if(!Auth::instance()->get_user()) {
            return $this->_forbidden('You\'re not logged in.');
        }
        return $pbefore;
    }

    public function action_get($comment_id=null) {
        $user = Auth::instance()->get_user();
        $user_id = $user && $user->loaded() ? $user->id : 0;
        if($comment_id) {
            $comment = ORM::factory('supplychain_comment', $comment_id);
            if($comment->loaded()) {
                $supplychain = ORM::factory('supplychain', $comment->supplychain_id);
                if($supplychain->loaded()) {
                    if($supplychain->user_can($user_id, Sourcemap::READ)) {
                        $this->response = $comment->as_array();
                        return;
                    } else return $this->_forbidden('You don\'t have access to that supplychain.');
                } else return $this->_bad_request('Invalid supplychain.');
            } else return $this->_not_found('Comment does not exist.');
        } elseif(isset($_GET['supplychain_id'])) {
            $supplychain = ORM::factory('supplychain', $_GET['supplychain_id']);
            if($supplychain->loaded() && $supplychain->user_can($user_id, Sourcemap::READ)) {
                $comments = $supplychain->comments->find_all();
                $comment_data = array();
                foreach($comments as $i => $comment) {
                    $arr = $comment->as_array();
                    $arr['username'] = $comment->user->username;
                    $comment_data[] = $arr;
                }
                $this->response = $comment_data;
            } else $this->_forbidden();
        } else return $this->_bad_request('Supplychain id or comment id required.');
    }

    public function action_post() {
        if(!(($user = Auth::instance()->get_user()) && $user->loaded())) {
            return $this->_forbidden('Commenting is permitted for registered users only.');
        }
        $posted = $this->request->posted_data;
        if(isset($posted->supplychain_id)) {
            $supplychain = ORM::factory('supplychain', $posted->supplychain_id);
            if($supplychain->loaded()) {
                if(isset($posted->comment_body)) {
                    if(strlen($posted->comment_body) > self::MAXLEN) {
                        return $this->_bad_request('Comment is too long.');
                    } else {
                        $comment = ORM::factory('supplychain_comment');
                        $comment->body = $posted->comment_body;
                        $comment->timestamp = time();
                        $comment->supplychain_id = $supplychain->id;
                        $comment->user_id = $user->id;
                        $comment->save();
                        $this->response = array('comment_id' => $comment->id);
                    }
                } else return $this->_bad_request('Missing comment body.');
            } else return $this->_bad_request('Invalid supplychain.');
        } else return $this->_bad_request('Missing supplychain_id');
    }

    public function action_delete($comment_id=null) {
        if(!Auth::instance()->get_user()) {
            return $this->_forbidden('You\'re not logged in.');
        } 
        $user = Auth::instance()->get_user();
        if(!$comment_id) return $this->_bad_request('Missing comment id.');
        $comment = ORM::factory('supplychain_comment', $comment_id);
        if($comment->loaded()) {
            $admin = ORM::factory('roles')->where('name', '=', 'administrator')->find();
            if(($comment->user_id === $user->id) || $user->has('roles', $admin)) {
                $comment->delete();    
            } else return $this->_forbidden();
        } else return $this->_not_found();
    }

}
