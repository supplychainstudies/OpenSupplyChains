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

    <meta name="description" content="The open directory of supply chains and carbon footprints" /> 
    <meta name="keywords" content="carbon footprint, supply chain, life-cycle assessment, transparency, traceability, sustainable, green products" />
    <meta name="author" content="The Sourcemap Team">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" > 
    <meta http-equiv="content-language" content="en-us">
    
    <meta name="viewport" content="user-scalable=no, width=device-width, maximum-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="apple-touch-icon" href="assets/images/favicon-large.png">
    <link rel="image_src" href="assets/images/favicon-large.png">
    <link rel="alternate" type="application/rss+xml" title="Sourcemap Blog Feed" href="http://blog.sourcemap.com/feed/" />
	<link rel="search" href="services/opensearch/" type="application/opensearchdescription+xml" title="Sourcemap.com" />
   
    <link rel="stylesheet" href="assets/styles/preview.css" type="text/css"/>

    <script type="text/javascript">
        // Get rid of address bar on iphone/ipod
        window.addEventListener("load",function() {
            // Set a timeout...
            setTimeout(function(){
                // Hide the address bar!
                window.scrollTo(0, 1);
            }, 0);
        });    
    </script>

</head>

<body id="preview">
<?= $content ?>
</body>
</html>
