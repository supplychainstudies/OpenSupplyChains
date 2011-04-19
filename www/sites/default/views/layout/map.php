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

    </head>
    

</head>

<body id="supplychain">
    <?= View::factory('partial/branding') ?>
    <header><?= Message::instance()->get() ? Message::instance()->render() : false ?></header>
    <div id="bar">
        <nav id="page-navigation">
            <h2><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h2>
            <div id="user-actions">
                    <div id="favorite-button"></div>
            </div>
            <ul id="metrics">
                <li>
                    <div class="metric-value">30.5</div>
                    <div class="metric-unit">CO2e</div>                        
                </li>
                <li>
                    <div class="metric-value">30.5</div>
                    <div class="metric-unit">H2O</div>                        
                </li>
                <li>
                    <div class="metric-value">3100</div>
                    <div class="metric-unit">USD</div>                        
                </li>
                <div class="clear"></div>
            </ul>
            <div class="clear"></div>
        </nav>
    </div> <!-- bar -->
    <div id="content">     
        <div id="map">
            <?= $content ?>
        </div>
        <ul id="map-actions">
            <li id="fullscreen-button">Fullscreen <div class="icon"></div></li>
            <li id="share-button">Share <div class="icon"></div></li>
        </ul>
        
        <div class="clear"></div>
        <div id="map-secondary-content">
            <div id="discussion-section">
                <h3>Comment</h3>
                <div id="comment-form">
                    <textarea id="comment-area"></textarea>
                </div>
                <input id="comment-submit" type="submit" text="Comment"/>
                <div class="clear"></div>
                <ul id="comments">
                    <li class="comment">
                        <div class="comment-meta">
                            <img class="user-avatar" src="http://www.gravatar.com/avatar/56b80ca11f9b256ad2b13de751c0ae9f?s=32" />
                            <p class="comment-meta-text">
                                Added by <a class="user-link" href="">hock</a> ten minutes ago.
                            </p>
                            <div class="clear"></div>                        
                        </div>
                        <div class="comment-body">
                            <p>This is a comment about sourcemap. Cool, cool, cool comment. I think that it is very commentable. Commentable? Is that even a word.</p>
                        </div>
                    </li>
                    <li class="comment">
                        <div class="comment-meta">
                            <img class="user-avatar" src="http://www.gravatar.com/avatar/56b80ca11f9b256ad2b13de751c0ae9f?s=32" />
                            <p class="comment-meta-text">
                                Added by <a class="user-link" href="">hock</a> ten minutes ago.
                            </p>
                            <div class="clear"></div>                        
                        </div>
                        <div class="comment-body">
                            <p>This is a comment about sourcemap. Cool, cool, cool comment. I think that it is very commentable. Commentable? Is that even a word.</p>
                        </div>
                    </li>
                    <li class="comment">
                        <div class="comment-meta">
                            <img class="user-avatar" src="http://www.gravatar.com/avatar/56b80ca11f9b256ad2b13de751c0ae9f?s=32" />
                            <p class="comment-meta-text">
                                Added by <a class="user-link" href="">hock</a> ten minutes ago.
                            </p>
                            <div class="clear"></div>                        
                        </div>
                        <div class="comment-body">
                            <p>This is a comment about sourcemap. Cool, cool, cool comment. I think that it is very commentable. Commentable? Is that even a word.</p>
                        </div>
                    </li>
                </ul>
            </div>
        
            <div id="linking-section">
                <div id="qrcode-badge">
                    <img class="qrcode" src="http://chart.apis.google.com/chart?chs=70x70&chf=bg,s,ffffffff&cht=qr&chl=http://www.sourcemap.org&choe=UTF-8&chld=L|0" />
                    <div class="qrcode-about">You can share this physically with a qrcode.</div>
                    <div class="clear"></div>
                </div>
                <h3>Similar Supplychains</h3>
                <ul id="similar-supplychains">
                    <li><a href="">Ikea Product Map</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                    <li><a href="">Ikea Product Map</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                </ul>
                <h3>Related Geographies</h3>
                <ul id="related-geographies">
                    <li><a href="">Ikea Product Map</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                    <li><a href="">Ikea Product Map</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                    <li><a href="">Ikea Product Map with details.</a></li>
                </ul>
            </div>
        <div class="clear"></div>
        </div>
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
  
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
  
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
  
</body>
</html>


