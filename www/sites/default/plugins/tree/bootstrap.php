<?php

Route::set('tree', 'tree/<id>')
    ->defaults(array(
        'controller' => 'tree',
        'action' => 'index'
    )
);
