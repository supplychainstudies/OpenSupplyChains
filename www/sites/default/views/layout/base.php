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

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags(
        'sites/default/assets/styles/reset.css',
        'sites/default/assets/styles/default.less',
        'assets/styles/general.less'
    )?>
   
    </head>
    <body>

    <div class="container">

        <div id="top-notice"></div>
        <?= View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        <div class="clear"></div>
        <div id="content">
             <header>
                <p><?= Breadcrumbs::instance()->get() ? Breadcrumbs::instance()->render() : false ?></p>
                <p><?= Message::instance()->get() ? Message::instance()->render() : false ?></p>
             </header>
             <div class="article-content">
                <?= isset($content) ? $content : '<h2>There\'s nothing here.</h2>' ?>
             </div>
             <aside id="sidebar">
             </aside>
             <nav id="secondary-navigation"></nav>
        </div>
        <footer id="footer">

        <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>
          
        <!--[if lt IE 7 ]>
            <script src="js/libs/dd_belatedpng.js"></script>
            <script> DD_belatedPNG.fix('img, .png_bg'); 
        <![endif]-->

    </div><!-- #container -->
</body>
</html>
