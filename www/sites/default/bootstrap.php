<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

if(!class_exists('Kohana')) die('Looks like we\'re missing Kohana.  Check your configuration and try again.');
/**
 * Set the site-specific routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('view', '<controller>/<id>', array(
    'controller' => '(view|embed|mobile|comment|create|edit|delete|list)'
))->defaults(array(
        'action' => 'index'
    )
);

Route::set('tree', '<controller>/<id>', array(
    'controller' => '(tree)'
))->defaults(array(
        'action' => 'index'
    )
);

Route::set('toplevel static maps', 'static/<id>.<sz>.png', array(
    'id' => '[a-z0-9]+', 'sz' => '(t|s|m|l|f|th-m)'
))->defaults(array(
    'controller' => 'map',
    'action' => 'static',
    'sz' => 'th-m'
));

Route::set('category browse', 'browse(/<category>)')
->defaults(array(
    'controller' => 'browse'
));

Route::set('tools/import/action', 'tools/import/<controller>(/<action>)')
    ->defaults(array(
        'directory' => 'tools/import',
        'action' => 'index'
    )
);

Route::set('userprofile', 'user/<id>')
    ->defaults(array(
        'controller' => 'user',
        'action' => 'index'
    )
);

Route::set('admin/dashboard', 'admin', array())
    ->defaults(array(
        'controller' => 'admin',
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

Route::set('admin/migrate/id/action', 'admin/migrate/<id>(/<action>)', array()
)->defaults(array(
    'directory' => 'admin', 
    'controller' => 'migrate', 
    'action' => 'details'
));


Route::set('admin/collection/action', 'admin/<controller>/<action>', array())
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'dashboard',
        'action' => 'index'
));

Route::set('static maps', 'map/static/<id>.<sz>.png', array(
    'id' => '[a-z0-9]+', 'sz' => '(t|s|m|l|o|th-m)'
))->defaults(array(
    'controller' => 'map',
    'action' => 'static',
    'sz' => 'th-m'
));

Route::set('wp', 'wp/<id>')
    ->defaults(array(
        'controller' => 'wp',
        'action' => 'index'
    )
);
