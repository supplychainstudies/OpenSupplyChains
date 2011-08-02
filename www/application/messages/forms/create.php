<?php

return array(
    'title' => array(
        'not_empty' => 'A title is required.'
    ),
    'description' => array(
        'not_empty' => 'Please enter a short description.',
        'min_length' => 'Please give a little more information about this map.',
        'max_length' => 'The description should be 80 characters or less.'
    ),
    'tags' => array(
        'regex' => 'Enter a list of tags separated by spaces.'
    ),
    'category' => array(
        'in_array' => 'It appears that category doesn\'t exist.'
    )
);
