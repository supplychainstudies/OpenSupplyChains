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
<base href="<?php URL::base() ?>" />
<title><?php HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>
<link href="sites/default/assets/styles/error.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="masthead">
<h1><?php HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h1>
<?php if(Breadcrumbs::instance()->get()): ?><?php Breadcrumbs::instance()->render() ?><?php endif; ?>
<?php if(Message::instance()->get()): ?><?php Message::instance()->render() ?><?php endif; ?>
</div>
<div id="main">
<?php $content ?>
</div>
<div id="footer">
</div>
</body>
</html>
