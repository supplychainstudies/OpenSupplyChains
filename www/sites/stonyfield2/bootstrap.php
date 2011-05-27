<?php
Route::set('sf-milk', 'milk')
    ->defaults(array(
        'controller' => 'welcome',
        'action' => 'milk'
    ));

Route::set('sf-default', '<id>')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));
