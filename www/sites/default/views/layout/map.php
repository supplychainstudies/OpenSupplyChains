<!DOCTYPE html>
<html style="height: 100%">
<head>
<base href="<?= URL::base() ?>" />
<title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>
<!-- Google Maps API -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!-- OpenLayers -->
<script type="text/javascript" language="javascript" src="assets/scripts/openlayers/OpenLayers.js"></script>
<!-- jQuery -->
<script type="text/javascript" language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<!-- Sourcemap -->
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap.js"></script>
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap/map.js"></script>
<script type="text/javascript" language="javascript" src="assets/scripts/sourcemap/supplychain.js"></script>
</head>
<body style="height: 100%;">
<div id="masthead">
<h1><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h1>
<?php if(Breadcrumbs::instance()->get()): ?><?= Breadcrumbs::instance()->render() ?><?php endif; ?>
<?php if(Message::instance()->get()): ?><?= Message::instance()->render() ?><?php endif; ?>
</div>
<div id="main" style="height: 100%; width: 100%; background-color: black;">
<?= $content ?>
</div>
<div id="footer">
</div>
</body>
</html>
