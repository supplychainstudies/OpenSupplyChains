<!doctype html>  
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <base href="<?= URL::base(true, true) ?>"></base>
    <title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>

    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 110%; color: #111; }
        #masthead .div {
            display: inline-block;
        }
        #masthead .logo-container {
            font-size: 200%;
        }
        #masthead #masthead-utility {
            display: inline;
            width: 60%;
        }
        a { text-decoration: none; color: #222; font-weight: bold;}
        a:hover { color: #eee; background-color: #333; }
        ul.flat-nav {
            list-style-type: none;
            display: inline-block;
            width: 100%;
            padding: 0;
            margin: 0;
            display: none;
        }
        ul.flat-nav > li {
            display: inline;
            margin-left: .2em;
        }
    </style>
</head>
<body class="fixed">
    <div id="masthead"><div class="logo-container"><a href="">Sourcemap</a></div><div id="masthead-utility">Browse | Create</div></div>
    <ul class="flat-nav" id="primary-nav">
        <li><a href="#">one</a></li>
        <li><a href="#">two</a></li>
        <li><a href="#">three</a></li>
    </ul>
    <?= $content ?>
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
</body>
</html>
