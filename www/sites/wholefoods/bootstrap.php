<?php
Route::set('wf-milk', 'milk')
    ->defaults(array(
        'controller' => 'welcome',
        'action' => 'milk'
    ));

Route::set('wf-default', '<id>')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));
