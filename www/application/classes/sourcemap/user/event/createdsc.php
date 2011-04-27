<?php
class Sourcemap_User_Event_Createdsc extends Sourcemap_User_Event {

    protected $_tag = 'createdsc';

    public function __construct($user_id, $scid, $timestamp=null) {
        $this->user_id = $user_id;
        $this->scid = $scid;
        $this->timestamp = $timestamp ? $timestamp : time();
    }

    protected function get_recipients() {
        //todo: friendz...?
        return array($this->user_id);
    }

    protected function get_data() {
        return array(
            'user_id' => $this->user_id,
            'supplychain_id' => $this->scid
        );
    }

    public static function load($data) {
        if(isset($data['user_id']) && ($user = ORM::factory('user', $data['user_id'])->find())) {
            $data['username'] = $user->username;
        }
        return $data;
    }
}
