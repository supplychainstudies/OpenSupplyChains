<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

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

$is_admin = false;
$user = ORM::factory('user', Auth::instance()->get_user());
$admin = ORM::factory('role')
        ->where('name', '=', 'admin')->find();
if($user->has('roles', $admin)) $is_admin = true;

if($is_admin){
    $title_max_length = array('max_length', array(10000));
    $desc_max_length = array('max_length', array(10000));
    $title_attributes = array(
        'maxlength' => -1,
        'placeholder' => 'Admin mode: Maximum 10000 characters.'
    );
    $desc_attributes = array(
        'maxlength' => -1,
        'placeholder' => 'Admin mode: Maximum 10000 characters.',
        'id'=>'form-description'
    );

}
else{
    $title_max_length = array('max_length', array(55));
    $desc_max_length = array('max_length', array(140));
    $title_attributes = array(
        'maxlength' => 55,
        'placeholder' => 'Maximum 55 characters.'
    );
    $desc_attributes = array(
        'maxlength' => 140,
        'placeholder' => 'Maximum 140 characters.',
        'id'=>'form-description'
    );
}

return array(
    'fields' => array(
        'title' => array(
            'type' => 'text',
            'label' => 'Title',
    		'attributes' => $title_attributes
        ),
        'description' => array(
            'type' => 'textarea',
            'label' => 'Description',
            'css_class' => array(
                'preview'
            ),
            'attributes' => $desc_attributes
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
        'title' => array(
            array('not_empty'),$title_max_length
        ),
        'category' => array(
            array('in_array', array($valid_cats))
        ),
        'description' => array(
            $desc_max_length
        )
    ),
    'filters' => array()
);
