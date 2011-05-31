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
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags(
        'sites/default/assets/styles/reset.css',
        'assets/styles/general.less',
        'sites/default/assets/styles/default.less',
        'sites/default/assets/styles/slider.css' // todo: this should only get loaded from welcome.php
    )?>
   
    </head>
    <body>
    <div class="overlay">
        <div id="registration" class="dialog">
            <div class="dialog-content">
                <?= View::factory('partial/registration', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="twelvecol">
                <div id="top-notice"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <?= View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    <header>
        <p><?= Breadcrumbs::instance()->get() ? Breadcrumbs::instance()->render() : false ?></p>
        <p><?= Message::instance()->get() ? Message::instance()->render() : false ?></p>
    </header>
    
    <?= isset($content) ? $content : '<h2>There\'s nothing here.</h2>' ?>
    
    <div class="clear"></div>

    <?= View::factory('partial/footer', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>
      
    <!--[if lt IE 7 ]>
        <script src="js/libs/dd_belatedpng.js"></script>
        <script> DD_belatedPNG.fix('img, .png_bg'); 
    <![endif]-->

</body>
</html>
