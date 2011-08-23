<div id="masthead" class="container">
    <div id="header">
        <div id="logo">
            <a name="home" href="">        
                <h1 id="site-title">Sourcemap</h1>
                <img src="/assets/images/logo.png" alt="sourcemap" />
            </a>
        </div>
        <ul id="navigation">
            <li> <a href="browse">Browse</a> </li>
            <li> <a href="create">Create</a> </li>
            <?php if($current_user = Auth::instance()->get_user()): ?>
            <li>
				<div> <a href="/home">Dashboard</a> </div>
                <a class="existing-login" href="auth/logout">Sign out</a>
            </li>
            <?php else: ?>
            <li class="register">
                <div class="button"> <a href="/register">Register</a> </div>
                <a class="existing-login" href="auth">Sign in.</a>
            </li>
        	<?php endif; ?>
        </ul>
        <form method="post" action="/search/">
            <div id="search-div">
                <input id="search" name="q" placeholder="Search" results="0" />
            </div>
        </form>
        <div id="search-results"></div>
        <div class="clear"></div>
    </div>    
</div> <!-- #masthead -->