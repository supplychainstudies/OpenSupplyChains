<?php

Route::set('tree', '<controller>/<id>')
    ->defaults(array(
        'controller' => 'tree',
        'action' => 'index'
    )
);
