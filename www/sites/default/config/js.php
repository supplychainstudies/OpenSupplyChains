<?php

return array(
    'packages' => array(
        'sourcemap-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/map/visualization/tour.js',
                'sites/default/assets/scripts/map/embed.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        )
    )
);
