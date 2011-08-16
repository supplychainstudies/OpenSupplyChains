<?php

return array(
    'packages' => array(
        'sourcemap-core' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/form.js',
                'sites/default/assets/scripts/curtain.js',
            )
        ),
        'sourcemap-edit' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/graph.js',
                'assets/scripts/sourcemap/supplychain/editor/tabbed.js',
                'sites/default/assets/scripts/supplychain/edit.js'
            ),
            'requires' => array(
                'sourcemap-core', 'sourcemap-template', 'sourcemap-map'
            )
        ),
        'sourcemap-create' => array(
            'scripts' => array(
                'assets/scripts/sourcemap/supplychain/create.js',
            ),
        ),
        'sourcemap-welcome' => array(
            'scripts' => array(
                'sites/default/assets/scripts/slider/anythingslider.js',
                'sites/default/assets/scripts/slider/easing.1.2.js',
                'sites/default/assets/scripts/welcome.js'
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
                'less', 'sourcemap-map'
            )
        ),
        'blog-view' => array(
            'scripts' => array(
                'sites/default/assets/scripts/map/blog.js',
                'assets/scripts/sourcemap/map/blog.js'
            ),
            'requires' => array(
                'sourcemap-map'
            )
        )
    )
);
