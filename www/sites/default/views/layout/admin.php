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
    border-bottom: 1px solid #222;
    height: 100%;
}
#masthead h1 {
    padding: .25em .5em .25em .5em;
    margin: 0;
}
#masthead a { text-decoration: none; }
.nav {
    text-align: right;
    padding-right: 1em;
}
.breadcrumbs {
    background-color: #eee;
    padding: .25em;
}

#extra {
    width: 100%;
}

#extra .status-messages {
    list-style-type: none;
    color: #333;
    margin: 0;
    padding: 0;
}

#extra .status-message {
    padding: .25em;
    border-bottom: 1px solid;
    font-weight: bold;
}

#extra .error {
    background-color: #ffc0c0;
    border-color: #ff8080;
}

#extra .warn {
    background-color: #ffff66;
    border-color: #ff9900;
}

#extra .success {
    background-color: #99ff99;
    border-color: #55ff55;
}

#extra .info {
    background-color: #9999ff;
    border-color: #5555ff;
}

#summary {
    width: 80%;
    margin: auto;
}

#summary strong, .good-news {
    color: #50aa50;
}
#summary strong.bad-news, .bad-news {
    color: #aa5050;
}

#main {
    padding: 1em 2em 1em 2em;
}

table {
}

tbody tr.odd td {
    background-color: #ccc;
    border-bottom: 1px solid #333;
}

tbody td {
    font-size: .8em;
    font-family: Helvetica, Arial, sans-serif;
    text-align: middle;
    padding: .5em;
}
fieldset {
    border: 1px solid #ddd;
}
.attr-list { 
    font-family: monospace;
}
.attr-list dt {
    border-bottom: .1em solid #ddd;
    font-weight: bold;
}
div.righty-tighty {
    float: right;
}
.taxonomy-tree {
    padding: 0;
    margin: 0;
}
.taxonomy-tree ul.children {
    list-style-type: none;
    margin: 0;
}
.taxonomy-tree > ul.children {
    border-left: 1px solid #333;
}
</style>
</head>
<body>
<div id="masthead">
<h1><a href="admin"><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></a></h1>
<div class="nav"><a href="auth/logout">log out</a>&nbsp;|&nbsp;<a href="">view site</a></div>
</div>
<div id="extra">
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
