<?php defined('SYSPATH') or die('No direct script access.');
require_once("classes/Stripe.php");
$instance = "default";

if(isset(Kohana::config('apis')->stripe_api_secret_key)){
    Stripe::setApiKey(Kohana::config('apis')->stripe_api_secret_key);
}
?>
