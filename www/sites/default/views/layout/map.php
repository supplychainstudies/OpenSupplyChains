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
        <div id="view-pane">

            <h3><a href="#">About</a></h3>
            <div>
                
                <h2 id="supplychain-title"><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></h2>       
                <?php if(Breadcrumbs::instance()->get()): ?><?= Breadcrumbs::instance()->render() ?><?php endif; ?>
                <?php if(Message::instance()->get()): ?><?= Message::instance()->render() ?><?php endif; ?>     
                
                <div id="creation-meta">
                    <img class="user-avatar" src="http://www.gravatar.com/avatar/56b80ca11f9b256ad2b13de751c0ae9f?s=32" />
                    Added by <a class="user-link" href="">hock</a> and modified ten months ago.
                </div>
                <div class="clear"></div>
                
                <div id="qrcode-badge">
                    <img class="qrcode" src="http://chart.apis.google.com/chart?chs=70x70&chf=bg,s,ffffffff&cht=qr&chl=http://www.sourcemap.org&choe=UTF-8&chld=L|0" />
                    <div class="clear"></div>
                </div>
                <h4 class="clear subtitle">Description</h4>
                <p id="supplychain-description">
                    Whats the carbon footprint of your IKEA bed? www.sourcemap.org knows: <a href="http://bit.ly/96m9uy">http://bit.ly/96m9uy</a>
                </p>

            </div>
            <h3><a href="#">Places</a></h3>
            <div id="place-list">
                <ul>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:90%"></div>
                        <div class="bar dollars" style="width:30%"></div>
                        <div class="bar water" style="width:50%"></div>         
                    </div>                               
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:20%"></div>
                        <div class="bar dollars" style="width:60%"></div>
                        <div class="bar water" style="width:10%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:30%"></div>
                        <div class="bar dollars" style="width:20%"></div>
                        <div class="bar water" style="width:10%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:10%"></div>
                        <div class="bar dollars" style="width:0%"></div>
                        <div class="bar water" style="width:5%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:90%"></div>
                        <div class="bar dollars" style="width:100%"></div>
                        <div class="bar water" style="width:70%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:20%"></div>
                        <div class="bar dollars" style="width:60%"></div>
                        <div class="bar water" style="width:10%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:40%"></div>
                        <div class="bar dollars" style="width:60%"></div>
                        <div class="bar water" style="width:40%"></div>         
                    </div>
                </li>
                <li class="place-item" id="place-unique-hash">
                    <div class="place-name">Place Type</div>
                    <div class="place-location">Place Location</div>
                    <div class="place-description">Place short description...</div>
                    <span class="place-details">More</span>
                    <div class="metric-bars">
                        <div class="bar carbon" style="width:30%"></div>
                        <div class="bar dollars" style="width:20%"></div>
                        <div class="bar water" style="width:10%"></div>         
                    </div>
                </li>
                </ul>
            </div>

            <h3><a href="#">Receipt</a></h3>
            <div id="supplychain-receipt">
                <ul>
                    <div id="carbon-summary-bar" class="receipt-summary-bar">
                        <div class="embodied-carbon segment" style="width:30%"></div>
                        <div class="transport-carbon segment" style="width:70%"></div>
                        <div class="clear"><div>
                    </div>
                    <li id="carbon-receipt" class="receipt-item">
                        <h3>Carbon Footprint</h3> 
                        <span class="metric-value">30.5</span>
                        <span class="metric-unit">kg co2e</span>
                    </li>
                    <li id="carbon-embodied-receipt" class="receipt-item minor">
                        <h3>Carbon Embodied</h3> 
                        <span class="metric-value">10.5</span>
                        <span class="metric-unit">kg co2e</span>
                    </li>
                    <li id="carbon-transport-receipt" class="receipt-item minor">
                        <h3>Carbon Transport</h3>  
                        <span class="metric-value">20</span>
                        <span class="metric-unit">kg co2e</span>
                    </li>
                    <li id="weight-receipt" class="receipt-item">
                        <h3>Weight</h3> 
                        <span class="metric-value">300.5</span>
                        <span class="metric-unit">kg</span>
                    </li>
                    <li id="distance-receipt" class="receipt-item">
                        <h3>Distance</h3> 
                        <span class="metric-value">5430</span>
                        <span class="metric-unit">km</span>
                    </li>
                </ul>
            </div>
            <h3><a href="#">Edit</a></h3>
            <div>
                <p>This map is <a href="">public</a>.<p>
                <p>It shows embodied and transport carbon calculations.</p>
            </div>
        </div>
        <div id="detail-pane">
            
            <div class="place-name">Place Type</div>
            <div class="place-location">Place Location</div>
              <div class="place-description">
              
                <p>Time to throw away that wrapping paper and put your new toys on the shelf, the holidays are over and its time for another look into the greatness that is the Vimeo Community.</p>
                
                <!--iframe src="http://player.vimeo.com/video/17679597" width="400" height="225" frameborder="0"></iframe--><p><a href="http://vimeo.com/17679597">"Sky High"</a> from <a href="http://vimeo.com/birg">Birg</a> on <a href="http://vimeo.com">Vimeo</a>.</p>
            </div>
            <h3 class="place-receipt-title">Receipt</h3>
           <ul class="place-metrics">
               <div class="metric-bars">
                      <div class="bar carbon" style="width:40%"></div>
                      <div class="bar dollars" style="width:100%"></div>
                      <div class="bar water" style="width:80%"></div>         
                  </div>
                  <li class="place-metric">
                      Carbon Footprint: <span class="metric-value">30.5</span>
                              <span class="metric-unit">kg co2e</span>
                  </li>
                  <li class="place-metric minor">
                      Carbon Embodied: <span class="metric-value">10.5</span>
                              <span class="metric-unit">kg co2e</span>
                  </li>
                  <li class="place-metric minor">
                      Carbon Transport: <span class="metric-value">20</span>
                              <span class="metric-unit">kg co2e</span>
                  </li>   
                  <li class="place-metric">
                      Weight: <span class="metric-value">930.5</span>
                              <span class="metric-unit">kg</span>
                  </li>
                  <li class="place-metric">
                      Distance: <span class="metric-value">3000</span>
                              <span class="metric-unit">km</span>
                  </li>  
              </ul>
            <div class="close">X</div>
        </div>
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
</body>

  
    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
  
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
  
</body>
</html>


