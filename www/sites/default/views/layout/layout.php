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
        'sites/default/assets/styles/general.less?v=2'
    ) ?>
</head>

<body class="fixed">
    <div id="top-notice"></div>
    <div id="branding">
        <a href="">        
        <header id="masthead">
            <h1>Sourcemap</h1>
        </header>
        </a>
        <nav id="main-navigation">
            <ul class="nav">
                <?php if($current_user = Auth::instance()->get_user()): ?>
                <li class="register-link"><span class="username"><?= HTML::chars($current_user->username) ?>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></li>
                <?php else: ?>
                <li class="register-link"><a href="register">Join us</a>  |&nbsp;<a class="rpxnow" onclick="return false;"
		     href="https://sourcemap1.rpxnow.com/openid/v2/signin?token_url=http%3A%2F%2F18.85.59.71%2Fsmap%2Ftrunk%2Fwww%2Fauth%2Floginopenid"> Social Sign In </a> 
		     or <a href="auth">Log in</a> or  <a href="auth/forgot_password">forgot password?</a></li>
                <?php endif; ?>
                <li id="browse-navigation">
                    <h3>Browse</h3>            
                    <ul>
                        <li><a href="">Sourcemaps</a></li>
                        <li><a href="">Tools</a>
                            <ul>
                                <li><a href="tools/import/csv">Importer</a></li>
                            </ul>
                        </li>
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
                <h1><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h1>                 
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

<script type="text/javascript">
  var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");
  document.write(unescape("%3Cscript src='" + rpxJsHost +
"rpxnow.com/js/lib/rpx.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
  RPXNOW.overlay = true;
  RPXNOW.language_preference = 'en';
</script>

  
</body>
</html>
