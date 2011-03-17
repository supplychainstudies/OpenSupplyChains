<?php

return array(
    'packages' => array(
        'sourcemap-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/map/visualization/tour.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
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
        'map-view' => array(
            'scripts' => array(
                'sites/default/assets/scripts/map/view.js'
            ),
            'requires' => array(
                'modernizr', 'less', 'sourcemap-map', 'sourcemap-template'
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
