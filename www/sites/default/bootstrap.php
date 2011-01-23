<?php
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('admin/users/id', 'admin/users/<id>', array('id' => '\d+'))
    ->defaults(array(
        'directory' => 'admin', 'controller' => 'users', 'action' => 'single'
    ));

/*Route::set('admin/users', 'admin/users(/<action>(/<id>))(/page/<page>)', 
    array('id' => '\d+', 'action' => 'index|single|delete', 'page' => '\d+'))
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'users',
    ));
*/

Route::set('admin/id', 'admin/<controller>(/<id>)', array(
        'id' => '\d+'
    ))->defaults(array(
        'directory' => 'admin', 'controller' => 'admin', 'action' => 'index'
    ));

Route::set('admin', 'admin/<controller>/<action>')
    ->defaults(array(
        'directory' => 'admin', 'controller' => 'admin', 'action' => 'index'
    ));

Sourcemap_JS::init();
Sourcemap_JS::add_packages(array(
    'default-view' => array(
        'scripts' => array(
            'site/default/assets/view.js'
        ),
        'requires' => array(
            'sourcemap-map', 'sourcemap-template'
        )
    )
));
