<div id="masthead" class="container_16">
    <div id="header" class="grid_16">
        <div id="logo">
            <a name="home" href="">        
                <h1 id="site-title">Sourcemap</h1>
                <img src="/assets/images/logo.png" alt="sourcemap" />
            </a>
        </div>
        <ul id="navigation">
            <li>
                <a href="browse">Browse</a>            
            </li>
            <li>
                <a href="create">Create</a>
            </li>
            <li class="register">
                <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                    <a href="/home"><?= HTML::chars($current_user->username) ?></a>&nbsp;|&nbsp;<a href="auth/logout">Log out</a></span>
                <?php else:  // Otherwise, this ?>
                <div class="button">
                    <a href="/register">Register</a> 
                </div>
                <a class="existing-login" href="auth">Existing user login</a>
                <?php endif; ?>
            </li>
        </ul>
        <form method="post" action="/search/">
            <fieldset>
                <div id="search">
                    <input name="q" placeholder="Search" results="0" />
                </div>
            </fieldset>
        </form>
        <div id="search-results"></div>
    </div>
</div> <!-- #masthead -->

