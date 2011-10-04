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
    'fields' => array(
        'email' => array(
            'type' => 'text',
            'label' => 'Email Address',
    		'attributes' => array(
    			'maxlength' => 100,
    			'placeholder' => 'Enter your email address.'				
    		)
        ),
        'username' => array(
            'type' => 'text',
            'label' => 'Username',
            'attributes' => array(
                'maxlength' => 32,
    			'placeholder' => 'That username is taken.'
            )
        ),
        'password' => array(
            'type' => 'password',
            'label' => 'Password'
        ),
        'password_confirm' => array(
            'type' => 'password',
            'label' => 'Password (Repeat)'
        ),
    	'recaptcha' => array(
    		'type' => 'recaptcha',
    		'label' => 'recaptcha_form'
    	),
    	'sourcemaporg_account' => array(
            'type' => 'text',
            'label' => 'If you had a Sourcemap.org user account, enter your old username here:'
        ),
        'confirm_terms' => array(
            'type' => 'checkbox',
            'label' => 'I have read and agree to the <a href="/info/terms/#document-content?w=500" target="_blank" class="modal">terms of service</a>.',
            'default' => 0
        ),
        'register' => array(
            'type' => 'submit',
            'label' => 'Register'
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
        'password' => array(
            array('not_empty')
        ),
        'password_confirm' => array(
            array('matches', array("password"))
        ),
        'confirm_terms' => array(
            array('not_empty')
        ),
    ),
    'filters' => array()
);
