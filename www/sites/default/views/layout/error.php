<!DOCTYPE html>
<html>
<head>
<base href="<?= URL::base() ?>" />
<title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>
<link href="sites/default/assets/styles/error.css" type="text/css" rel="stylesheet" />
</head>
<body>
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
