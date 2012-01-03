<?php

Route::set('hello', 'hello')
    ->defaults(array(
        'controller' => 'hello',
        'action' => 'index'
    )
);


