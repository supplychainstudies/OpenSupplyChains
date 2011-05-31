    <div id="branding" class="container">
        <div class="row">
            <div class="threecol">
                <a href="">        
                    <header id="masthead">
                        <img src="/assets/images/logo.png" alt="sourcemap" />
                    </header>
                </a>
            </div>
            <div id="header-search-div" class="fivecol">
                <form method="post" action="/search/">
                    <input id="header-search" type="search" results="0" placeholder="Search" />
                    <div id="livesearch"></div>
                </form>
            </div>
            <nav id="header-nav">
                <div class="onecol">
                    <a href="browse">Browse</a>            
                </div>
                <div class="onecol">
                    <a href="info">Info</a>
                </div>
                <div class="onecol last">
                    <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                    <a href="/home"><?= HTML::chars($current_user->username) ?></a>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                    <?php else:  // Otherwise, this ?>
                    <a href="/register">Register</a> | <a href="auth">Log in</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div> <!-- .row -->
    </div> <!-- #branding .container -->
    <div class="clear"></div>

