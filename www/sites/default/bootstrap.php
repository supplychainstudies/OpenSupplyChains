<?php
if(!class_exists('Kohana')) die('stop. drop. shut \'em down. whoa. that\'s how ruff ryders roll.');
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('tools/tool/action', 'tools/<controller>/<action>')
    ->defaults(array(
        'directory' => 'tools',
        'controller' => 'tools',
        'action' => 'index'
    ));

Route::set('admin/collection/id/action', 'admin/<controller>(/<id>(/<action>))', array(
        'id' => '\d+', 
        'action' => 'delete|add'))
    ->defaults(array(
        'directory' => 'admin', 
        'controller' => 'admin', 
        'action' => 'single'
    ));

Route::set('admin/collection/id', 'admin/<controller>/<id>', array(
        'id' => '\d+'
    ))->defaults(array(
        'directory' => 'admin', 
        'controller' => 'admin', 
        'action' => 'index'
    ));

Route::set('admin/collection', 'admin/<controller>')
    ->defaults(array(
        'directory' => 'admin', 
        'controller' => 'admin', 
        'action' => 'index'
    ));

Sourcemap_JS::add_packages(array(
    'map-view' => array(
        'scripts' => array(
            'sites/default/assets/scripts/map/view.js'
        ),
        'requires' => array(
            'modernizr', 'less', 'sourcemap-map', 'sourcemap-template'
        )
    )
));
