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

return array(
    'title' => array(
        'not_empty' => 'A title is required.'
    ),
    'description' => array(
        'not_empty' => 'Please enter a short description.',
        'min_length' => 'Please give a little more information about this map.',
        //'max_length' => 'The description reach maximum characters limit.'
    ),
    'tags' => array(
        'regex' => 'Enter a list of tags separated by spaces.'
    ),
    'category' => array(
        'in_array' => 'It appears that category doesn\'t exist.'
    )
);
