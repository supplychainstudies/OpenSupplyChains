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
?>

<!doctype html>  
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <base href="<?= URL::base(true, true) ?>" />
    <title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>

	<meta name="description" content="" /> 
	<meta name="keywords" content="" />
    <meta name="author" content="">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" > 
	<meta http-equiv="content-language" content="en-us">
	
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="apple-touch-icon" href="assets/images/favicon-large.png">
    <link rel="image_src" href="assets/images/favicon-large.png">
    
    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>    
</head>

<body id="embedded-supplychain">
<?= $content ?>


    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>

    <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
    <![endif]-->
  
    <script>
        Sourcemap.embed_supplychain_id = <?= isset($supplychain_id) ? $supplychain_id : '"null"' ?>;
        Sourcemap.embed_params = <?= isset($embed_params) ? json_encode($embed_params) : '{}' ?>;
    </script>
	<? if(isset($embed_params) && $embed_params['served_as'] == "earth") { ?>
		<script type="text/javascript" src="http://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22earth%22%2C%22version%22%3A%221%22%7D%5D%7D&key=<?= Kohana::config('apis')->earth_api_key; ?>"></script>
	<? } ?>
</body>
</html>
