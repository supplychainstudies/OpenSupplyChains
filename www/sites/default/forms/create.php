<?php

// category options
$taxonomy = Sourcemap_Taxonomy::load_tree();
    
$cat_opts = array();
$cat_opts[] = array(0, 'None');

$valid_cats = array(0);

$flat_cats = Sourcemap_Taxonomy::flatten();

$_p = array();
foreach($flat_cats as $i => $cat) {
    list($id,$nm,$title,$depth) = $cat;
    if($depth < count($_p)) {
        while(count($_p) > $depth) array_pop($_p);
    }
    $_p[] = $title;
    $valid_cats[] = $id;
    if($id)
        $cat_opts[] = array($id, join(' / ', array_slice($_p, 1)));
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
                'maxlength' => 140,
				'placeholder' => 'Maximum 140 characters.'
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
