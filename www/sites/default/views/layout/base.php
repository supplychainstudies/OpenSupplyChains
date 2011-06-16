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
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/placeholders/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>

</head>
<body class="main">
    <div id="wrapper">
        <!--div class="overlay">
            <div id="registration" class="dialog">
                <div class="dialog-content">
                    <?= true ? "" : View::factory('register', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
                </div>
            </div>
        </div-->

        <?= View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        <div class="container_16">
            <div class="messages grid_16">
                <header>
                    <p><?= Breadcrumbs::instance()->get() ? Breadcrumbs::instance()->render() : false ?></p>
                    <p><?= Message::instance()->get() ? Message::instance()->render() : false ?></p>
                </header>
            </div>
        </div>
            
        <div class="clear"></div>
        
        <?= isset($content) ? $content : '<h2>There\'s nothing here.</h2>' ?>
        <div id="push"></div>
    </div><!-- #wrapper -->
    <div class="spacer"></div>
    <div id="footer">
         <?= View::factory('partial/footer', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>
      
    <!--[if lt IE 7 ]>
        <script src="js/libs/dd_belatedpng.js"></script>
        <script> DD_belatedPNG.fix('img, .png_bg'); 
    <![endif]-->

</body>
</html>
