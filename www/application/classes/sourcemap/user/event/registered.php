<?php
class Sourcemap_User_Event_Registered extends Sourcemap_User_Event {

    protected $_tag = 'registered';

    public function __construct($user_id) {
        parent::__construct();
        $this->user_id = $user_id;
    }

    protected function get_recipients() {
        return array($this->user_id);
    }

    protected function get_data() {
        return array(
            'user_id' => $this->user_id
        );
    }
}
