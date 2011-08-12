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
            'label' => 'Title'
        ),
        'description' => array(
            'type' => 'textarea',
            'label' => 'Description',
            'css_class' => array(
                'preview'
            ),
            'attributes' => array(
                'maxlength' => 80
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
        'create' => array(
            'type' => 'submit',
            'label' => 'Create'
        )
    ),
    'messages_file' => 'forms/create',
    'rules' => array(
        // todo: wildcard
        //'*' => array(),
        'title' => array(
            array('not_empty')
        ),
        'category' => array(
            array('in_array', array($valid_cats))
        ),
        'description' => array(
            array('in_array', array($valid_cats))
        )
    ),
    'filters' => array()
);
