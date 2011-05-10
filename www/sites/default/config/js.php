<?php

return array(
    'packages' => array(
        # moved to global js package config
        /*'sourcemap-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/map/visualization/tour.js',
                'assets/scripts/sourcemap/map/embed.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map', 'showdown'
            )
        ),*/
        'sourcemap-edit' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/supplychain/editor/tabbed.js',
                'sites/default/assets/scripts/supplychain/edit.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
        'sourcemap-create' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/create.js',
            ),
        ),
        'sourcemap-welcome' => array(
            'scripts' => array(
                'sites/default/assets/scripts/slider/anythingslider.min.js',
                'sites/default/assets/scripts/slider/easing.1.2.js',
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
                'less', 'sourcemap-map', 'sourcemap-template'
            )
        ),
        'sourcemap-social' => array(
            'scripts' => array(
                'sites/default/assets/scripts/social.js'
            ),
            'requires' => array(
                'sourcemap-jquery'
            )
        )
    )
);
