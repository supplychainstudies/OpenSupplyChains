<?php
class Model_Supplychain_Comment extends ORM {

    // todo: comment flags, e.g. 'suspended'/'deleted', 'flagged for removal'?

    public $_belongs_to = array(
        'supplychain' => array(
            'model' => 'supplychain',
            'foreign_key' => 'supplychain_id',
            'far_key' => 'id'
        ),
        'user' => array(
            'model' => 'user'
        )
    );
}
