<?php

return array(
    'packages' => array(
        'tree-view' => array(
            'scripts' => array(
                'sites/default/plugins/tree/assets/scripts/tree/view.js',
                'sites/default/plugins/tree/assets/scripts/libs/d3/d3.js',
                'sites/default/plugins/tree/assets/scripts/libs/d3/d3.geom.js',
                'sites/default/plugins/tree/assets/scripts/libs/d3/d3.layout.js'
            ),
            'requires' => array(
                'less', 'sourcemap-core','openlayers'
            )
        )
    )
);
