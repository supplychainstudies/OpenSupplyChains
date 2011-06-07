<?php
class Model_User_APIKey extends Sourcemap_ORM {
    public $_table_names_plural = false;

    public $_created_column = array(
        'column' => 'created',
        'format' => true
    );

    public $_belongs_to = array(
        'user' => array(
            'model' => 'user', 'foreign_key' => 'user_id'
        )
    );

}
