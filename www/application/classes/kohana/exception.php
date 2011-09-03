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

class Kohana_Exception extends Exception {

    /**
     * Creates a new translated exception.
     *
     *     throw new Kohana_Exception('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string     error message
     * @param   array      translation variables
     * @param   integer    the exception code
     * @return  void
     */
    public function __construct($message, array $variables = NULL, $code = 0)
    {
    	// Set the message
    	$message = __($message, $variables);

    	// Pass the message to the parent
        $code = (integer)$code;
    	parent::__construct($message, $code);
    }

    /**
     * Magic object-to-string method.
     *
     *     echo $exception;
     *
     * @uses    Kohana::exception_text
     * @return  string
     */
    public function __toString()
    {
    	return Kohana::exception_text($this);
    }

} 
