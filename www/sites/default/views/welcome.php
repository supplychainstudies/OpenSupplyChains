<?php // Returns featured maps ?>
<div class="clear"></div>
<div class="container dark-background">
    <div class="row featured">
        <?= View::factory('partial/thumbs/slider', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="spacer"></div>

<div class="container">
    <div class="row">
        <div class="twelvecol intro">
            <h1>Sourcemap is a platform for researching, optimizing and sharing supply chains.</h1>
            <p>Integer consectetur turpis eu orci convallis volutpat. Sed accumsan mattis urna et dictum. In blandit, sapien id cursus vehicula, nisi arcu convallis tortor, sed pharetra turpis orci interdum quam. Cras vitae est velit. Vestibulum commodo gravida orci non rhoncus. Aliquam consequat orci eget risus blandit vitae convallis urna blandit.</p>
        </div>
    </div>
</div>

<div class="spacer"></div>

<div class="container">
    <div class="row">
        <div class="twelvecol">
            <h3>Popular Today</h3>
        </div> 
    </div>
    <div class="row">
        <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="spacer"></div>
<div class="container">
    <div class="row">
        <div class="twocol">
            <h3>Recent</h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        </div>
        <div class="twocol">
            <h3>&nbsp;</h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        </div>
        <div class="twocol">
            <h3>&nbsp;</h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        </div>
        <div class="onecol"></div>
        <div class="fivecol last">
            <div class="news">
                <h3>From Headquarters</h3>
                <ul>
                    <li>
                        <img src="http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg" alt="" />
                        <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
                    </li>
                    <li>
                        <img src="http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg" alt="" />
                        <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
                    </li>
                    <li>
                        <img src="http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg" alt="" />
                        <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
                    </li>
                </ul>
            </div>
        </div><!-- .fourcol -->
    </div><!-- .row -->
</div><!-- .container -->

<div class="clear">&nbsp;</div>
