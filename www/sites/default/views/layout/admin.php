<!DOCTYPE html>
<html>
<head>
<base href="<?= URL::base(true, true) ?>" />
<title>Administration - <?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>
<style>
html, body { margin: 0; padding: 0; }
body {
    font-family: sans-serif;
}
#masthead {
    margin: 0;
    padding: 0;
    color: #333;
    background-color: #ddd;
    border-bottom: 1px solid #072;
    height: 100%;
}
#masthead h1 {
    padding: .25em .5em .25em .5em;
    margin: 0;
}
.breadcrumbs {
    background-color: #eee;
    padding: .25em;
}
#main {
    padding: 1em 2em 1em 2em;
}

tbody tr.odd {
    background-color: #eee;
}

tbody td {
    font-family: monospace;
    text-align: middle
}
fieldset {
    border: 1px solid #ddd;
}
</style>
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
