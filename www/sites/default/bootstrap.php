<?php
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('admin', 'admin(/<controller>(/<id>))')
    ->defaults(array(
        'directory' => 'admin', 'controller' => 'admin', 'action' => 'index'
    ));
