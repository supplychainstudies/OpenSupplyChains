<?php

return array(
    'packages' => array(
        'sourcemap-embed' => array(
            'scripts' => array(
                #'http://maps.google.com/maps/api/js?sensor=false&v=3.2',
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/map/visualization/tour.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        )
    )
);
