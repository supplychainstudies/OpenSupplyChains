<?php defined('SYSPATH') or die('No direct script access.');
require_once("classes/Stripe.php");
$instance = "default";

$config = Kohana::config('stripe.'.$instance);
    Stripe::setApiKey($config['apikey']);
?>
