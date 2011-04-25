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
        'assets/styles/general.less'
    ) ?>
</head>
<body class="fixed">
    <div id="top-notice"></div>
    <?= View::factory('partial/branding', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    <div id="content">          
         <article>
             <header>
                <p><?= Breadcrumbs::instance()->get() ? Breadcrumbs::instance()->render() : false ?></p>
                <p><?= Message::instance()->get() ? Message::instance()->render() : false ?></p>
             </header>
             <div class="article-content">             
                 <p><?= $content ?></p>
             </div>
             <footer></footer>
         </article>
         <aside id="sidebar">
             <h3>Sidebar</h3>
             <p>...</p>
         </aside>
         <nav id="secondary-navigation"></nav>
    </div>
    <footer id="footer">
        <div id="footer-content">
        <div id="footer-callouts">
            <div class="footer-box">
                <h3>Contact us</h3>
                <p>Interested in helping? Partnering? Just have some questions? Contact us at <a>info[at]sourcemap[dot]org</a>.</p> 
            </div>
            <div class="footer-box">               
                <h3>Creative Commons</h3>
                <p>All of our user generated content (maps, comments, etc.) is licensed <a href="">BY-SA Creative commons 3.0</a>.</p> 
            </div>
            <div class="footer-box">               
                <h3>Open Data</h3>
                <p>Sourcemap is committed to open data. All of our data is <a href="">PDDL (1.0)</a> and available at <a href="">data.sourcemap.org</a>.</p> 
            </div>
            <div class="clear"></div>
            <p>Sourcemap (TM). For more information on how we use your data, read our <a href="">privacy policy</a>. For more information on how you can use the site, read our <a href="">terms of service</a>.</p>
        </div>
        <div id="footer-about">
            <h3>About Sourcemap</h3>
            <p>We built Sourcemap so that everyone can access the information needed to make sustainable choices and share them with the world. The project is free, opensource, and volunteer driven. For more news, check the <a href="">Sourcemap Blog</a>.</p>
        </div>    
        <div class="clear"></div>
        </div>
    </footer>
    <div class="overlay">
        <div class="dialog">
            <header class="dialog-header error">
            <h3>This is a dialog title</h3>
            <div class="close">X</div>
            </header>
            <div class="dialog-content">
                This is some dialog content
            </div>
            <footer>
                <button class="button">Done</button> 
            </footer>
        </div>                
    </div>
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
</body>
</html>
