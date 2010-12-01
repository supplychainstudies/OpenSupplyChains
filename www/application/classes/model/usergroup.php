<?php
class Usergroup extends ORM {
    protected $_has_many = array(
        'members' => array(
            'model' => 'user',
            'through' => 'user_usergroup'
        )
    );
}
