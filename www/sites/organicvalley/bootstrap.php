<?php
Route::set('ov-milk', 'milk')
    ->defaults(array(
        'controller' => 'welcome',
        'action' => 'milk'
    ));

Route::set('ov-default', '<id>')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));
