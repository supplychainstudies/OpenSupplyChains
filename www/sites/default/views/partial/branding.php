    <div id="masthead" class="container_16">
        <div class="grid_4">
            <a href="">        
                <header id="logo">
                    <img src="/assets/images/logo.png" alt="sourcemap" />
                </header>
            </a>
        </div>
        <div id="header" class="grid_12">
            <nav>
                <ul>
                    <li class="first">
                        <a href="browse">Browse</a>            
                    </li>
                    <li>
                        <a href="create">Create</a>
                    </li>
                    <li>
                        <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                            <a href="/home"><?= HTML::chars($current_user->username) ?></a>
                        &nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                        <?php else:  // Otherwise, this ?>
                        <a href="/register">Register</a> | <a href="auth">Log in</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
            <form method="post" action="/search/">
                <button onclick="('masthead-search').submit(); return false;" type="button" id="search-button" dir="ltr" role="button">
                    <span>Search</span>
                </button>
                <label>
                    <input id="search" type="search" results="0" />
                </label>
            </form>
            <div id="livesearch"></div>
        </div> <!-- #header -->
    </div> <!-- #branding -->
    <div class="clear"></div>

