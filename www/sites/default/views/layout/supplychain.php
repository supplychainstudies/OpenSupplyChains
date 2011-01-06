<!DOCTYPE html>
<html style="height: 100%">
<head>
<base href="<?= URL::base(true, true) ?>" />
<title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>
<!-- Google Maps API -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!-- OpenLayers -->
<script type="text/javascript" language="javascript" src="assets/scripts/openlayers/OpenLayers.js"></script>
<!-- jQuery -->
<script type="text/javascript" language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<!-- jQuery ui -->
<script type="text/javascript" language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
<!-- Sourcemap -->
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap.js"></script>
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap/supplychain.js"></script>
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap/supplychain/editor/form.js"></script>
<link stype="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css" rel="stylesheet" />
<style>
body {
    font-family: sans-serif;
    font-size: 120%;
}
h1 {
    border-bottom: 4px solid #66bb66;
    background-color: #eee;
    padding: 0 .5em 0 .5em;
}
button, .input-text {
    font-size: 120%;
}
.input-text {
    color: #339933;
}
fieldset {
    border: 1px solid #ccc;
}
.formeditor-stop {
    padding: 1.5em;
    border-bottom: 1px solid #a88;
}
.formeditor-stop h3 {
    color: #fff;
    background-color: #333;
    padding: .25em;
}
.lonlat {
    font-family: monospace;
    font-style: italic;
    color: #aaa;
}
</style>
</head>
<body style="height: 100%;">
<div id="masthead">
<h1><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h1>
<?php if(Breadcrumbs::instance()->get()): ?><?= Breadcrumbs::instance()->render() ?><?php endif; ?>
<?php if(Message::instance()->get()): ?><?= Message::instance()->render() ?><?php endif; ?>
</div>
<div id="main">
<?= $content ?>
</div>
<div id="footer">
</div>
</body>
</html>
