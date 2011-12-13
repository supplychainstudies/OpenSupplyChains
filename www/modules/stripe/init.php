<?php defined('SYSPATH') or die('No direct script access.');
require_once("classes/Stripe.php");
$instance = "default";

Stripe::setApiKey(Kohana::config('apis')->stripe_api_secret_key);
?>
