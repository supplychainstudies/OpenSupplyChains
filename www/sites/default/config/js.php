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

return array(
    'packages' => array(
        'sourcemap-core' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/form.js',
                'sites/default/assets/scripts/effects.js',
            )
        ),
        'sourcemap-create' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/create.js',
            ),
        ),
        'sourcemap-welcome' => array(
            'scripts' => array(
                'sites/default/assets/scripts/slider/anythingslider.js',
                'sites/default/assets/scripts/slider/easing.1.2.js',
                'sites/default/assets/scripts/welcome.js'
            ),
            'requires' => array(
                'sourcemap-jquery'
            )
        ),
        'map-view' => array(
            'scripts' => array(
                'sites/default/assets/scripts/map/view.js'
            ),
            'requires' => array(
                'less', 'sourcemap-map'
            )
        ),
        'blog-view' => array(
            'scripts' => array(
                'sites/default/assets/scripts/map/blog.js',
                'assets/scripts/sourcemap/map/blog.js'
            ),
            'requires' => array(
                'sourcemap-map'
            )
        )
    )
);
