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
                <a href="browse">Browse</a>            
            </li>
            <li id="info-navigation">
                <a href="info">Info</a>
            </li>
            <li id="account-navigation">
                <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                <span class="username"><?= HTML::chars($current_user->username) ?>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                <?php else:  // Otherwise, this ?>
                <a href="register">Register</a> | <a href="auth">Log in</a>
                <?php endif; ?>
            </li>
        </ul>
        <div class="clear"></div>
        
    </div> <!-- branding -->
   

