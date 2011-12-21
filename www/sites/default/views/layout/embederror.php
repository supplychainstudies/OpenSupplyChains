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

<!DOCTYPE html>
<html>
<head>
<title>Page Not Found</title>
<base href="<?= URL::base() ?>" />
<style>
    body {
	    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;		
	    font-size:1em;
	    line-height:1em;
	    margin:0 auto;
	    background: #fff url("http://www.sourcemap.com/assets/images/background.png") repeat-x;
	    color: #3c3c3c;
	}
	img {
		display: block; 
		margin-left: auto;
	    margin-right: auto; }
    h4{text-align:center;}
	div {
		margin-top: 20%;
	}
</style>
</head>
<body>
	<div>
		<img src="http://www.sourcemap.com/assets/images/logo.png" />
		<h4><? if(Message::instance()->get()) { ?><?= Message::instance()->render(); ?><?php } else { echo "Sourcemap Not Found"; } ?></h4>
	</div>
<script type="text/javascript"> if (window.console) { var error = "$e"; console.log(error); } </script>
</body>
</html>

