<?php
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
    $args = $argv;
    array_shift($args);
    foreach($args as $i => $arg) {
        print "$arg\n";
        include('t/'.basename($arg));
    }
    list($total, $good, $bad) = sm_test();
    print "\n>>>\n";
    print "$good / $total tests passed\n";
    print ".\n";
}
