<?php
return array(
    'packages' => array(
        'wholefoods-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/map/visualization/tour.js',
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
        'wholefoods-imap' => array(
            'scripts' => array(
                'sites/wholefoods/assets/scripts/jquery.maphilight.min.js'
            ),
            'requires' => array(
                'jquery'
            )
        )
    )
);
