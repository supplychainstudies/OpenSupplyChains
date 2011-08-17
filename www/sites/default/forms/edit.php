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
			'attributes' => array(
				'maxlength' => 140,
				'placeholder' => 'Maximum 140 characters.'				
			),
            'css_class' => array(
                'preview'
            )
        ),
        'tags' => array(
            'type' => 'text',
            'label' => 'Tags',
	        'attributes' => array(
				'placeholder' => 'Separated by spaces.'
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
            array('max_length', array(140))
        )
    ),
    'filters' => array()
);
