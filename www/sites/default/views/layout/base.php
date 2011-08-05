<!doctype html>  
<html lang="en" class="no-js">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <base href="<?= URL::base(true, true) ?>" />
    <title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>

    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow&v1' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>

</head>
<body class="main">
    <div id="wrapper">
        <div class="overlay">
            <div id="auth" class="dialog">
                <div class="dialog-content">
                </div>
            </div>
        </div>

        <?= View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        <div class="container">
            <div class="messages">
                <header>
                    <p><?= Breadcrumbs::instance()->get() ? Breadcrumbs::instance()->render() : false ?></p>
                    <p><?= Message::instance()->get() ? Message::instance()->render() : false ?></p>
                </header>
            </div>
        </div>
        <?= isset($content) ? $content : '<h2>There\'s nothing here.</h2>' ?>
        <div class="push"></div>
    </div><!-- #wrapper -->
    <div id="footer">
         <?= View::factory('partial/footer', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less', 'sourcemap-core') ?>
      
    <!--[if lt IE 7 ]>
        <script src="js/libs/dd_belatedpng.js"></script>
        <script> DD_belatedPNG.fix('img, .png_bg'); 
    <![endif]-->

</body>
</html>
