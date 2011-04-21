<?php
class Sourcemap_User_Event_Announce extends Sourcemap_User_Event {

    protected $_tag = 'announce';

    public function __construct($message) {
        parent::__construct();
        $this->message = $message;
    }

    protected function get_recipients() {
        return array(array(null, Sourcemap_User_Event::EVERYBODY));
    }

    protected function get_data() {
        return array(
            'message' => $this->message
        );
    }
}
