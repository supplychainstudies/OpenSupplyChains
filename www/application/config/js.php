<?php
return array(
    'packages' => array(
        'jquery' => array(
            'scripts' => array(
                'assets/scripts/libs/jquery/jquery.js'
            )
        ),
        'jquery-ui' => array(
            'scripts' => array(
                'assets/scripts/libs/jquery/jquery-ui.js'
            ),
            'requires' => array(
                'jquery'
            )
        ),
        'jqote' => array(
            'scripts' => array(
                'assets/scripts/libs/jquery/jquery.jqote.js'
            ),
            'requires' => array(
                'jquery'
            )
        ),
		'google-analytics' => array(
            'scripts' => array(
                'assets/scripts/extra/analytics.js',
            )
        ),
        'showdown' => array(
            'scripts' => array(
                'assets/scripts/libs/showdown.js',
            )
        ),
        'sourcemap-jquery' => array(
            'scripts' => array(
                'assets/scripts/libs/jquery/jquery.js',
                'assets/scripts/libs/jquery/jquery-ui.js',
                'assets/scripts/libs/jquery/jquery.jqote.js'
            )
        ),
        'sourcemap-core' => array(
            'scripts' => array(
                'assets/scripts/sourcemap.js', 
                'assets/scripts/sourcemap/supplychain.js',
                'assets/scripts/sourcemap/supplychain/graph.js'
            ),
            'requires' => array(
                'less', 'sourcemap-search', 'sourcemap-jquery', 'google-analytics'
            )
        ),
        'sourcemap-search' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/search.js'
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
                'assets/scripts/sourcemap/map.js',
                'assets/scripts/sourcemap/map/base.js',
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-core', 
                'google-maps', 'twitter', 'openlayers-cloudmade'
            )
        ),
	    'sourcemap-map-edit' => array(
	        'scripts' => array(
	            'assets/scripts/sourcemap/map/magicwords.js',
	            'assets/scripts/sourcemap/map.js',
	            'assets/scripts/sourcemap/map/base.js',
	            'assets/scripts/sourcemap/map/editor.js',
	        ),
	        'requires' => array(
	            'sourcemap-template', 'sourcemap-core', 
	            'google-maps', 'twitter', 'openlayers-cloudmade'
	        )
	    ),
        'sourcemap-tabbed-edit' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/geocode.js'
            ),
            'requires' => array(
                'sourcemap-map'
            )
        ),
        'sourcemap-working' => array(
            'scripts' => array(
            ),
            'requires' => array(
                'sourcemap-core', 'sourcemap-template'
            )
        ),
        'openlayers' => array(
            'scripts' => array(
                'assets/scripts/libs/openlayers/OpenLayers.custom.js'
            )
        ),
        'openlayers-cloudmade' => array(
            'scripts' => array(
                'assets/scripts/libs/openlayers/cloudmade.js'
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
	    'twitter' => array(
	        'scripts' => array(
	            'http://widgets.twimg.com/j/2/widget.js'
	        )
	    ),
        'less' => array(
            'scripts' => array(
                'assets/scripts/libs/less/less.js'
            ),
            'env' => array(
                Sourcemap::DEV
            )
        ),
        'modernizr' => array(
            'scripts' => array(
                'assets/scripts/libs/modernizr/modernizr.js'
            )
        ),
        'sourcemap-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-map', 'showdown'
            )
        ),
        'sourcemap-mobile' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'sites/default/assets/scripts/map/mobile.js'
            ),
            'requires' => array(
                'sourcemap-map', 'showdown'
            )
        )
    )
);

