<?php
class Model_User_Profile extends ORM {
    
    const EMAILPUB = 1;
    
    public $_table_names_plural = false;

    protected $_belongs_to = array(
        'user' => array(
            'model' => 'user'
        )
    );

    public function validate() {
        return true;
    }
    
}
