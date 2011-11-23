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

$email = "";
$username = "";


if ($user = ORM::factory('user', Auth::instance()->get_user())){
    $email = $user->email; 
    $username = $user->username; 
}

return array(
    'fields' => array(
        'email' => array(
            'type' => 'text',
            'label' => 'Email Address',
    		'attributes' => array(
                'value' => $email,
    			'maxlength' => 100,
    			'placeholder' => 'Enter your email address.'				
    		)
        ),
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
            'attributes' => array(
                'value' => $username,
                'maxlength' => 32,
            )
        ),
        'concerning' => array(
            'type' => 'select',
            'label' => 'What is this concerning?',
            'attributes' => array(
                'options' => "wow", array(
                    'eh' => 'eh',
                    'eh2' => 'eh',
                    'eh3' => 'eh',
                    'eh4' => 'eh',
                    'eh5' => 'eh'
                )
            )
        ),
        'message' => array(
            'type' => 'textarea',
            'label' => 'Message',
            'attributes' => array(
    			'placeholder' => 'Enter your questions or comments.'
            )
        ),
    	'recaptcha' => array(
    		'type' => 'recaptcha',
    		'label' => 'Please type the two words below:'
    	),
        'contact' => array(
            'type' => 'submit',
            'label' => 'Contact Us'
        )
    ),
    'messages_file' => 'forms/register',
    'rules' => array(
        'email' => array(
            array('email'),
            array('not_empty')
        ),
        'username' => array(
            array('not_empty'), 
    		array('alpha_dash')
        ),
    ),
    'filters' => array()
);
