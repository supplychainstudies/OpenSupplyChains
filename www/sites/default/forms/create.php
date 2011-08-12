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
            'label' => 'Title',
			'attributes' => array(
				'maxlength' => 55,
				'placeholder' => 'Maximum 55 characters.'				
			)
        ),
        'description' => array(
            'type' => 'textarea',
            'label' => 'Description',
            'css_class' => array(
                'preview'
            ),
            'attributes' => array(
                'maxlength' => 144,
				'placeholder' => 'Maximum 144 characters.'
            )
        ),
        'tags' => array(
            'type' => 'text',
            'label' => 'Tags',
	        'attributes' => array(
				'placeholder' => 'As many as you want, separated by spaces.'
	        )
        ),
        'category' => array(
            'type' => 'select',
            'label' => 'Category',
            'options' => $cat_opts,
            'default' => 0
        ),
        'publish' => array(
            'type' => 'checkbox',
            'label' => 'Public',
            'default' => 0
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
