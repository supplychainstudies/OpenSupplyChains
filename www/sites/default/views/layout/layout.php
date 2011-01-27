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

    <!-- link rel="stylesheet" href="assets/styles/style.css?v=2">
    <link rel="stylesheet/less" href="assets/styles/sourcemap.less?v=2" type="text/css"-->
    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags('assets/styles/style.css', 'assets/styles/sourcemap.less?v=2') ?>
</head>

<body id="supplychain" class="fixed">
    <div id="branding">
        <header id="masthead">
            <h1>Sourcemap</h1>
        </header>
        <nav id="main-navigation">
            <ul class="nav">
                <li class="register-link"><a href="">Join us</a> or <a href="">Log in</a></li>          
                <li id="browse-navigation">
                    <h3>Browse</h3>            
                    <ul>
                        <li><a href="">Sourcemaps</a></li>
                        <li>
                            <a href="">Carbon Catalogue</a>
                            <ul>
                                <li><a href="">Part Catalogue</a></li>
                                <li><a href="">Transport Cat.</a></li>
                                <li><a href="">Power Cat.</a></li>
                                <li><a href="">Process Cat.</a></li>
                                <li><a href="">Endoflife Cat.</a></li>                                                                     
                            </ul>
                        </li>
                        <li><a href="">Members</a></li>      
                        <li><a href="">Groups</a></li>                    
                                      
                    </ul>
                </li>
                <li id="info-navigation">
                    <h3>Info</h3>
                    <ul>
                        <li><a href="">About Us</a></li>
                        <li><a href="">Who We Are</a></li>
                        <li><a href="">Join Us!</a></li>
                        <li><a href="">API and Code</a></li>
                        <li>
                            <a href="">Help</a>
                            <ul>
                                <li><a href="">Data References</a></li>
                                <li><a href="">FAQs</a></li>                                    
                            </ul>                                
                        </li>
                        <li><a href="">Get In Touch</a></li>                    
                    </ul>
                </li>
            </ul>
            <div id="search"><input id="search-field" type="search" results="0" placeholder="Search" /></div>
        </nav>
        <div class="clear"></div>
        
    </div> <!-- branding -->
   
    <div id="bar">
        <nav id="page-navigation">
            <h2><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h2>
            <div class="clear"></div>
        </nav>
    </div> <!-- bar -->
    <div id="content">          
         <article>
             <header>
                <p><?php 
                    if(Breadcrumbs::instance()->get()): ?>
                        <?= Breadcrumbs::instance()->render() ?>
                    <?php endif; ?>
                </p>
                <?php if(Message::instance()->get()): ?>
                    <?= Message::instance()->render() ?>
                <?php endif; ?>
                 
             </header>
             <p><?= $content ?></p>
             
             <footer></footer>
         </article>
         <aside id="sidebar">
             <h3></h3>
             <p></p>
         </aside>
         <nav id="secondary-navigation">
        
         </nav>
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
</body>

    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : Sourcemap_JS::script_tags('less') ?>

  
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
  
</html>
