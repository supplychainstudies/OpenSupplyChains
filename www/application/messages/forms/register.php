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
    'email' => array(
        'not_empty' => 'A valid email address is required',
        'email' => 'A valid email address is required'
    ),
    'username' => array(
        'default' => 'Please enter a unique username'
    ),
    'password' => array(
        'not_empty' => 'Please enter a password'
    ),
    'password_confirm' => array(
        'default' => 'Your password needs to match'
    ),
    'confirm_terms' => array(
        'not_empty' => 'Please agree to the terms' 
    )
);
