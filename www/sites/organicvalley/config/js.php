<?php
return array(
    'packages' => array(
        'organicvalley-embed' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js'
            ),
            'requires' => array(
                'sourcemap-template', 'sourcemap-map'
            )
        ),
        'organicvalley-imap' => array(
            'scripts' => array(
                'sites/organicvalley/assets/scripts/jquery.maphilight.min.js'
            ),
            'requires' => array(
                'jquery'
            )
        )
    )
);
