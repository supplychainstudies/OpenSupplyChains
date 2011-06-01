    <div id="branding" class="container_16">
        <div class="grid_4">
            <a href="">        
                <header id="masthead">
                    <img src="/assets/images/logo.png" alt="sourcemap" />
                </header>
            </a>
        </div>
        <div id="header-search-div" class="grid_7">
            <form method="post" action="/search/">
                <input id="header-search" type="search" results="0" placeholder="Search" />
                <div id="livesearch"></div>
            </form>
        </div>
        <nav id="header-nav">
            <div class="grid-5">
                <ul>
                    <li>
                        <a href="browse">Browse</a>            
                    </li>
                    <li>
                        <a href="create">Create</a>
                    </li>
                    <li>
                        <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                        <a href="/home"><?= HTML::chars($current_user->username) ?></a>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                        <?php else:  // Otherwise, this ?>
                        <a href="/register">Register</a> | <a href="auth">Log in</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </nav>
    </div> <!-- #branding .container -->
    <div class="clear"></div>

