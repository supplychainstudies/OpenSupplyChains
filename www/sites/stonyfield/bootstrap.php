<?php

Route::set('sf-default', '<id>')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));
