<?php // Returns featured maps ?>
<div class="clear"></div>
    <div class="featured">
        <?= View::factory('partial/thumbs/slider', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
</div>

<div class="spacer"></div>

<div class="container_16 center">
    <div class="grid_11">
        <h1>See where things <span class="highlight">come from</span>.</h1>
        <p>Simply put, we believe that people have the right to know where things come from, what they're made of, and how they affect the environment.  Using freely available data, Sourcemap helps you understand these complex interactions at a glance.</p>  
    </div>
    <div class="grid_5">
        <h1>&nbsp;</h1>
        <div class="aside">
            <p>Contribute to the world's first free and open carbon mapping application.</p>
            <p class="button"><a href="/register">Create an Account</a></p>
            <p class="small">(Already have one? <a href="/login">Sign in.</a>)</p>
        </div>
    </div>
</div>

<div class="spacer"></div>

<div class="container_16">
    <div class="grid_16">
        <h2 class="section">Popular Today</h2>
    </div>
</div>
    <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    
<div class="container_16">
    <div class="grid_3">
        <h2 class="section">Recent</h2>
        <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    <div class=" grid_3">
        <h3>&nbsp;</h3>
        <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    <div class="grid_3">
        <h3>&nbsp;</h3>
        <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    <div class="grid_1">&nbsp;</div>
    <div class="grid_6">
        <?php if($news): ?>
            <h3 class="section">News From Headquarters</h3>
            <div class="news">
                <ul>
                    <?php foreach($news as $i => $news_item): ?>
                    <li class="news-item">
                        <img src="assets/images/favicon.ico" alt="" />
                        <h5 class="title"><?= HTML::chars($news_item->title) ?></h5>
                        <p>
                            
                            <?= substr(HTML::chars($news_item->body), 0, 140) ?><br />
                            <a href="http://blog.sourcemap.com">&raquo Read more</a>
                        </p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div><!-- .container_16 -->

<div class="clear">&nbsp;</div>
