<?php

// category options
$taxonomy = Sourcemap_Taxonomy::load_tree();
    
$cat_opts = array();
$cat_opts[] = array(0, 'None');

$valid_cats = array(0);

foreach($taxonomy->children as $ti => $t) {
    $valid_cats[] = $t->data->id;
    $cat_opts[] = array($t->data->id, $t->data->name);
}

return array(
    'fields' => array(
        'title' => array(
            'type' => 'text',
            'label' => 'Map Title'
        ),
        'description' => array(
            'type' => 'textarea',
            'label' => 'Map Full Description',
            'attributes' => array(
                "maxlength" => 80
            ),
            'css_class' => array(
                'preview'
            )
        ),
        'tags' => array(
            'type' => 'text',
            'label' => 'Tags'
        ),
        'category' => array(
            'type' => 'select',
            'label' => 'Category',
            'options' => $cat_opts,
            'default' => 0
        ),
        'publish' => array(
            'type' => 'checkbox',
            'label' => 'Public'
        ),
        'save' => array(
            'type' => 'submit',
            'label' => 'Save'
        )
    ),
    'messages_file' => 'forms/create',
    'rules' => array(
        'title' => array(
            array('not_empty')
        ),
        'category' => array(
            array('in_array', array($valid_cats))
        ),
        'description' => array(
            array('max_length', array(80))
        )
    ),
    'filters' => array()
);
