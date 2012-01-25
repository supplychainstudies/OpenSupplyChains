<?php

return array(
    'packages' => array(
        'tree-view' => array(
            'scripts' => array(
                'sites/default/plugins/tree/assets/scripts/tree/view.js',
                'assets/scripts/libs/d3/d3.js',
                'assets/scripts/libs/d3/d3.geom.js',
                'assets/scripts/libs/d3/d3.layout.js'
            ),
            'requires' => array(
                'less', 'sourcemap-core','openlayers'
            )
        )
    )
);
