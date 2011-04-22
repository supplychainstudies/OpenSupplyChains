<?php
class Model_User_Favorite extends Sourcemap_ORM {
    public $_belongs_to = array(
        'user' => array(
            'model' => 'user', 'foreign_key' => 'user_id'
        ),
        'supplychain' => array(
            'mode' => 'supplychain', 'foreign_key' => 'supplychain_id'
        )
    );
}
