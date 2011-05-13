    <div id="branding">
        <a href="">        
        <header id="masthead">
            <h1>Sourcemap</h1>
        </header>
        </a>
        <div id="search">
            <input id="search-field" type="search" results="0" placeholder="Search" />
        </div>
        <!-- todo: this should be dynamic -->
        <ul class="nav">
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
            <li id="account-navigation">
                <h3>Account</h3>
                <ul>
                    <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                    <li class="register-link">
                        <span class="username"><?= HTML::chars($current_user->username) ?>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                    </li>
                    <?php else:  // Otherwise, this ?>
                    <li class="register-link">
                        <a href="register">Join us</a> or <a href="auth">Log in</a>
                    </li>
                    <?php endif; ?>
            
            </li>
        </ul>
        <div class="clear"></div>
        
    </div> <!-- branding -->
   

