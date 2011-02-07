<?php
class Model_Usergroup extends ORM {
    protected $_has_many = array(
        'members' => array(
            'model' => 'user',
            'through' => 'user_usergroup',
	    'far_key' => 'user_id'
        )
    );
    protected $_belongs_to = array(
        'owner' => array(
            'model' => 'user',
            'foreign_key' => 'owner_id'
	    )
	);
}
