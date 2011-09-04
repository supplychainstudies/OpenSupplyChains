<?php require('lib/src-client.php'); include('lib/extra/class.krumo.php');?>
<?php $src = new SrcClient(); ?>

<!doctype html>  
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <title>Sourcemap API Client Example (PHP)</title>   
 	<style> body { width:960px; margin:0 auto; font-family:sans-serif; }</style>
</head>

<body class="main">
	<h1>Sourcemap API Client</h1>
		<p>This is an example page for a simple php client to the <a href="http://www.sourcemap.com">Sourcemap.com</a> API. Here we create a new SrcClient and use it to perform some basic searches, and get information about a particular supplychain. The results are delivered using json, so we json_ecode that into a native php object that we can work with.</p>
		
	<h3>Declare a new Sourcemap API Client</h3>
	<blockquote>
		<code>$src = new SrcClient(); print($src);</code><br/>
		<code><strong><?php print($src); ?></strong></code>
	</blockquote>

	<h3>List available services</h3>
	<blockquote>
		<code>$src->available();</code>
		<?php krumo($src->available()); ?>
	</blockquote>

	<h3>Get supplychain id 744</h3>
	<blockquote>
		<code>$src->supplychain(744);</code>
		<?php krumo($src->supplychain(744)); ?>
	</blockquote>

	<h3>Get a list of supplychains</h3>
	<blockquote>
		<code>$src->supplychains(25);</code>
		<?php krumo($src->supplychains(25)); ?>
	</blockquote>

	<h3>Get supplychains that match a search</h3>
	<blockquote>
		<code>$src->supplychains("laptop");</code>
		<?php krumo($src->supplychains("laptop")); ?>
	</blockquote>
</body>
</html>