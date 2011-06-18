<?php // Returns featured maps ?>
<div class="clear"></div>
<div id="featured-maps">
    <?= View::factory('partial/thumbs/slider', array('data' => $featured)) ?>
</div>

<div class="spacer"></div>

<div class="container_12">
    <div id="site-description" class="grid_8">
        <h1 id="site-tagline">Where things come from.</h1>
        <p id="site-blurb">We believe that people have the right to know where things come from, what they're made of, and how they affect the environment. Using freely available data, Sourcemap helps you understand these complex interactions at a glance.</p>  
    </div>
    <div class="grid_4">
        <div class="aside">
            <?php if($current_user = Auth::instance()->get_user()): // This happens if the user is logged in ?>
                <p>Welcome back, <?= HTML::chars($current_user->username) ?>!</p>
                <br/>
                <a href="/home" class="small">Your dashboard</a>&nbsp;|&nbsp;<a href="auth/logout" class="small">Log out</a>
            <?php else:  // Otherwise, this ?>
                <p class="button"><a href="/register">Register</a></p><br/>
                <p class="small">(Have an account? <a href="/auth">Log in.</a>)</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="spacer"></div>

<div class="container_16">
    <div class="grid_16">
        <h2 class="section">Popular</h2>
    </div>
</div>
<?= View::factory('partial/thumbs/featured', array('data' => $featured)) ?>

<div class="spacer"></div><div class="spacer"></div>

<div class="container_16">
    <div class="grid_9">
        <h2 class="section">Recent</h2>
    </div>
    <div class="grid_1">&nbsp;</div>
    <div class="grid_6">
        <h2>News</h2>
    </div>
</div>


<div id="recent-maps" class="container_16">
    <div class="preview-map-section grid_3">
        <?= View::factory('partial/thumbs/new', array('recent' => $recent, 'l' => 3, 'o' => 0)) ?>
    </div>
    <div class="preview-map-section grid_3">
        <?= View::factory('partial/thumbs/new', array('recent' => $recent, 'l' => 3, 'o' => 3)) ?>
    </div>
    <div class="preview-map-section grid_3">
        <?= View::factory('partial/thumbs/new', array('recent' => $recent, 'l' => 3, 'o' => 6)) ?>
    </div>
    
    <div class="grid_1">&nbsp;</div>
    <div class="grid_6">
        <?php if($news): ?>
            <div class="news">
                <ul>
                    <?php foreach($news as $i => $news_item): ?>
                    <li class="news-item">
                        <h5 class="title"><a href="http://blog.sourcemap.com"><?= HTML::chars($news_item->title) ?></a></h5>
                        <div class="clear"></div>
                        <p>
                            <?= substr(HTML::chars($news_item->body), 0, 130) ?>&hellip; 
                            <a class="readmore" href="http://blog.sourcemap.com">More &raquo;</a>
                        </p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div><!-- .container_16 -->

<div class="clear">&nbsp;</div>
