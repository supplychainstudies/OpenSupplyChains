<?php
return array(
    'packages' => array(
        'stonyfield-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
        'stonyfield-imap' => array(
            'scripts' => array(
                'sites/stonyfield/assets/scripts/jquery.maphilight.min.js'
            ),
            'requires' => array(
                'jquery'
            )
        )
    )
);
