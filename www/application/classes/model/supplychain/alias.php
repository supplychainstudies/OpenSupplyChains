<?php
/**
 *  Supply Chain Alias Model
 */
class Model_Supplychain_Alias extends ORM {
    public $_table_name = 'supplychain_alias';
    public $_table_names_plural = false;

    protected $_belongs_to = array(
        'supplychain' => array(
            'model' => 'supplychain'
        )
    );
}
