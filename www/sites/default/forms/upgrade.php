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
        'card-name' => array(
            'type' => 'text',
            'label' => 'Name on Card',
            'attributes' => array(
                'maxlength' => 140,
            )
        ),
        'card-number' => array(
            'type' => 'text',
            'label' => 'Card Number',
            'attributes' => array(
                'maxlength' => 20,
            )
        ),
        'card-cvc' => array(
            'type' => 'text',
            'label' => 'Security Code',
            'css_class' => array('third-width '), 
            'attributes' => array(
                'maxlength' => 5,
                'boxwidth' => 'third'
                'placeholder' => ' xxx';
            )
        ),
        'card-expiry-month' => array(
            'type' => 'text',
            'label' => 'Month',
            'css_class' => array('third-width '), 
            'attributes' => array(
                'maxlength' => 5,
                'boxwidth' => 'third'
                'placeholder' => ' xx';
            )
        ),
        'card-expiry-year' => array(
            'type' => 'text',
            'label' => 'Year',
            'css_class' => array('third-width '), 
            'attributes' => array(
                'maxlength' => 4,
                'boxwidth' => 'third'
                'placeholder' => ' xxxx';
            )
        ),
        'confirm_terms' => array(
            'type' => 'checkbox',
            'label' => 'I have read and agree to the <a href="/info/terms/#document-content?w=500" target="_blank" class="modal">terms of service</a>.',
            'default' => 0
        ),
        'upgrade' => array(
            'type' => 'submit',
            'label' => 'Upgrade'
        )
    ),
    'messages_file' => 'forms/upgrade',
    'rules' => array(
    ),
    'filters' => array()
);
