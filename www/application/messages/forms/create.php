<?php

return array(
    'title' => array(
        'not_empty' => 'A title is required.'
    ),
    'teaser' => array(
        'not_empty' => 'Please enter a short description.',
        'min_length' => 'Please give a little more information about this map.',
        'max_length' => 'The short description should be 140 characters or less.'
    ),
    'tags' => array(
        'regex' => 'Enter a list of tags separated by spaces.'
    ),
    'category' => array(
        'in_array' => 'It appears that category doesn\'t exist.'
    )
);
