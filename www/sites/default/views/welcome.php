<h1>Welcome!</h1>
<p><?= HTML::chars($message) ?></p>
<pre>
<?= print_r($supplychains->as_array('id', array('created', 'modified')), true) ?>
