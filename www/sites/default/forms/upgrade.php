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
        'card-number' => array(
            'type' => 'text',
            'label' => 'Card Number',
            'attributes' => array(
                'maxlength' => 20
            )
        ),
        'card-cvc' => array(
            'type' => 'text',
            'label' => 'Security Code (CVC)',
            'attributes' => array(
                'maxlength' => 5
            )
        ),
        'card-expiry-month' => array(
            'type' => 'text',
            'class' => 'halfwidth',
            'label' => 'Month',
            'attributes' => array(
                'maxlength' => 5
            )
        ),
        'card-expiry-year' => array(
            'type' => 'text',
            'class' => 'halfwidth',
            'label' => 'Year',
            'attributes' => array(
                'maxlength' => 4
            )
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
