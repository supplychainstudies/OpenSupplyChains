<?php
if(!class_exists('Kohana')) die('stop. drop. shut \'em down. whoa. that\'s how ruff ryders roll.');
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('tools/import/action', 'tools/import/<controller>(/<action>)')
    ->defaults(array(
        'directory' => 'tools/import',
        'action' => 'index'
    ));

Route::set('admin/dashboard', 'admin', array())
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'dashboard',
        'action' => 'index'
));

Route::set('admin/collection', 'admin/<controller>', array())
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'dashboard',
        'action' => 'index'
));

Route::set('admin/collection/id/action', 'admin/<controller>/<id>(/<action>)', array(
    'id' => '\d+'
))->defaults(array(
    'directory' => 'admin', 
    'controller' => 'dashboard', 
    'action' => 'details'
));

Route::set('admin/collection/action', 'admin/<controller>/<action>', array())
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'dashboard',
        'action' => 'index'
));

