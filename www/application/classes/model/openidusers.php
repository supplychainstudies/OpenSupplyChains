<?php
class Model_Openidusers extends ORM {

    protected $_belongs_to = array(
        'openuser' => array(
            'model' => 'user',
            'foreign_key' => 'user_id'
	    )
	);
}
