<?php
return array(
    'packages' => array(
        'jquery' => array(
            'scripts' => array(
                'assets/scripts/jquery/jquery.js'
            )
        ),
        'jquery-ui' => array(
            'scripts' => array(
                'assets/scripts/jquery/jquery-ui.js'
            ),
            'requires' => array(
                'jquery'
            )
        ),
        'jqote' => array(
            'scripts' => array(
                'assets/scripts/jquery/jquery.jqote.js'
            ),
            'requires' => array(
                'jquery'
            )
        ),
        'showdown' => array(
            'scripts' => array(
                'assets/scripts/showdown.js',
            )
        ),
        'sourcemap-jquery' => array(
            'scripts' => array(
                'assets/scripts/jquery/jquery.js',
                'assets/scripts/jquery/jquery-ui.js',
                'assets/scripts/jquery/jquery.jqote.js'
            )
        ),
        'sourcemap-core' => array(
            'scripts' => array(
                'assets/scripts/sourcemap.js', 
                'assets/scripts/sourcemap/supplychain.js'
            ),
            'requires' => array(
                'less', 'sourcemap-search', 'sourcemap-jquery', 'openlayers' 
            )
        ),
        'sourcemap-search' => array(
            'scripts' => array(
                'assets/scripts/search.js'
            ),
            'requires' => array(
                'sourcemap-jquery'
            )
        ),
        'sourcemap-template' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/template.js'
            ),
            'requires' => array(
                'sourcemap-core'
            )
        ),
        'sourcemap-map' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/map/magicwords.js',
                'assets/scripts/sourcemap/map.js'
            ),
            'requires' => array(
                'sourcemap-core', 'google-maps', 'openlayers-cloudmade'
            )
        ),
        'sourcemap-tabbed-edit' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/geocode.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
        'sourcemap-working' => array(
            'scripts' => array(
                'assets/scripts/script.js'
            ),
            'requires' => array(
                'sourcemap-core', 'sourcemap-template'
            )
        ),
        'openlayers' => array(
            'scripts' => array(
                'assets/scripts/openlayers/OpenLayers.js'
            )
        ),
        'openlayers-cloudmade' => array(
            'scripts' => array(
                'assets/scripts/openlayers/cloudmade.js'
            ),
            'requires' => array(
                'openlayers'
            )
        ),
        'google-maps' => array(
            'scripts' => array(
                'http://maps.google.com/maps/api/js?sensor=false&v=3.2'
            )
        ),
        'less' => array(
            'scripts' => array(
                'assets/scripts/less/less.js'
            ),
            'env' => array(
                Sourcemap::DEV
            )
        ),
        'modernizr' => array(
            'scripts' => array(
                'assets/scripts/modernizr/modernizr.js'
            )
        ),
        'sourcemap-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/map/visualization/tour.js',
                'assets/scripts/sourcemap/map/embed.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map', 'showdown'
            )
        )
    )
);

