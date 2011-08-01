<div id="masthead" class="container">
    <div id="header">
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
            <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
            <li>
                <a href="/home">Dashboard</a>
                <br />
                <a class="existing-login" href="auth/logout">Log out</a>
            </li>
            <?php else:  // Otherwise, this ?>
            <li class="register">
                <div class="button">
                    <a href="/register">Register</a> 
                </div>
                <a class="existing-login" href="auth">Existing user login</a>
                <?php endif; ?>
            </li>
        </ul>
        <form method="post" action="/search/">
            <div id="search">
                <input name="q" placeholder="Search" results="0" />
            </div>
        </form>
        <div id="search-results"></div>
    </div>
    <div class="clear"></div>
    
</div> <!-- #masthead -->

