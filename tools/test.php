<?php
# Copyright (C) Sourcemap 2011
# This program is free software: you can redistribute it and/or modify it under the terms
# of the GNU Affero General Public License as published by the Free Software Foundation,
# either version 3 of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License along with this
# program. If not, see <http://www.gnu.org/licenses/>.

error_reporting(E_ALL | E_STRICT);

function sm_test() { // ($label, $expected, $got) {
    static $success = 0;
    static $failure = 0;
    static $count = 0;
    $args = func_get_args();
    if(count($args) !== 3) {
        return array($count, $success, $failure);
    }
    list($label, $expected, $got) = $args;
    $result = $expected === $got ? true : false;
    $count++;
    if($result) $success++;
    else $failure++;
    echo "\n### $count:$label\n";
    echo "result: ".($result ? 'success' : 'failure')."\n";
    echo "expected: $expected\n";
    echo "got: $got\n";
    echo ".\n";
}

if(php_sapi_name() === 'cli') {
    define('SOURCEMAP_DIR', getenv('SOURCEMAP_DIR') ? 
        rtrim(getenv('SOURCEMAP_DIR'), '/').'/' : dirname(__FILE__).'/');
    define('SUPPRESS_REQUEST', true);
    require_once(SOURCEMAP_DIR.'/www/index.php');
    $args = $argv;
    array_shift($args);
    foreach($args as $i => $arg) {
        include(SOURCEMAP_DIR.'t/'.basename($arg));
    }
    list($total, $good, $bad) = sm_test();
    print "\n>>>\n";
    print "$good / $total tests passed\n";
    print ".\n";
}
