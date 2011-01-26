<?php
if(!class_exists('Kohana')) die('stop. drop. shut \'em down. whoa. that\'s how ruff ryders roll.');
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('admin/users/id', 'admin/users(/<id>(/<action>))', array('id' => '\d+', 'action' => 'delete|add'))
    ->defaults(array(
        'directory' => 'admin', 'controller' => 'users', 'action' => 'single'
    ));



Route::set('admin/id', 'admin/<controller>(/<id>)', array(
        'id' => '\d+'
    ))->defaults(array(
        'directory' => 'admin', 'controller' => 'admin', 'action' => 'index'
    ));

Route::set('admin', 'admin/<controller>/<action>')
    ->defaults(array(
        'directory' => 'admin', 'controller' => 'admin', 'action' => 'index'
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
