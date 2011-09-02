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

class Sourcemap_Job_Queue {

    public static $_instances = array();

    public static function instance($host, $port) {
        if(!isset(self::$_instances[$host][$port])) {
            if(!isset(self::$_instances[$host])) self::$_instances[$host] = array();
            $inst = new Sourcemap_Job_Queue($host, $port);
            self::$_instances[$host][$port] = $inst;
        }
        return self::$_instances[$host][$port];
    }

    public $host = '127.0.0.1';
    public $port = 11300;

    public function __construct($host=null, $port=null) {
        $this->client = new Sourcemap_Beanstalk_Producer($host, $port);
        $this->host = $host;
        $this->port = $port;
    }

    public function enqueue(Sourcemap_Job $job) {
        return $this->client->put($job->get_serialized_data());
    }
}
