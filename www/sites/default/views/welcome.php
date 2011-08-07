<?php // Returns featured maps ?>
<div class="clear"></div>
<div id="featured-maps">
    <?= View::factory('partial/thumbs/slider', array('data' => $featured)) ?>
</div>

<div class="spacer"></div>

<div class="container">
    <div id="sidebar">
        <div class="container">
            <h2>Popular Sourcemaps</h2>
        </div>
        <hr />
        <div class="container">
            <div class="preview-map-section">
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $recent1)) ?>
            </div>
        </div>
             
        <div class="spacer clear"></div>

        <div class="container">
            <h2>Recent Sourcemaps</h2>
        </div>
        <hr />
        <div class="container">
            <div class="preview-map-section">
                <?= View::factory('partial/thumbs/featured-vertical', array('supplychains' => $recent1)) ?>
            </div>
        </div>

        <div class="clear"></div>
    </div>
    <div id="body-content">
        <h1 id="site-tagline">Find out where things come from.</h1>
        <p id="site-blurb">Welcome to the website for sharing supply chains and understanding how they impact our communities and the environment. Join us to learn more and contribute.</p>  

        <div class="spacer"></div>

        <h2 class="section-title highlighted">Featured Sourcemaps</h2>
        <hr />
        <?= View::factory('partial/thumbs/featured', array('supplychains' => $popular)) ?>
        
        <?php //News Section ?>
        <?php if(isset($news) && $news && isset($news->posts)): ?>
            <div class="news">
                <h2>Sourcemap News</h2>
                <ul>
                    <?php foreach($news->posts as $i => $news_item): ?>
                    <li class="news-item">
                        <h5 class="title"><a href="http://blog.sourcemap.com"><?= HTML::chars($news_item->title_plain) ?></a></h5>
                        <p>
                            <?= substr(HTML::chars($news_item->excerpt), 0, 130) ?>&hellip; 
                            <a class="readmore" href="<?= $news_item->url ?>">More &raquo;</a>
                        </p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="clear">&nbsp;</div>
