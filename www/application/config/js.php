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
        'sourcemap-core' => array(
            'scripts' => array(
                'assets/scripts/sourcemap.js'
            ),
            'requires' => array(
                'jqote', 'jquery-ui', 'openlayers'
            )
        ),
        'sourcemap-map' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/map.js'
            ),
            'requires' => array(
                'sourcemap-core'
            )
        ),
        'openlayers' => array(
            'scripts' => array(
                'assets/scripts/openlayers/OpenLayers.js'
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
        )
    )
);

