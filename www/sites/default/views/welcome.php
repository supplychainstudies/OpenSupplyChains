<?php // Returns featured maps ?>
<div class="container dark-background">
    <div class="row featured">
        <ul id="slider">
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
        </ul><!-- #slider -->
    </div><!-- .row -->
</div><!-- .container -->

<div class="spacer"></div>

<div class="container">
    <div class="row">
        <div class="onecol"></div>
        <div class="tencol">
            <h1>Sourcemap helps you understand where things come from.</h1>
            <p>Nam congue rutrum diam non malesuada. Mauris lobortis magna eget libero sollicitudin suscipit. In consectetur adipiscing ligula, nec ultricies mauris volutpat eget. </p>
            <p>Integer consectetur turpis eu orci convallis volutpat. Sed accumsan mattis urna et dictum. In blandit, sapien id cursus vehicula, nisi arcu convallis tortor, sed pharetra turpis orci interdum quam. Cras vitae est velit. Vestibulum commodo gravida orci non rhoncus. Aliquam consequat orci eget risus blandit vitae convallis urna blandit.</p>
            <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
        </div>
        <div class="onecol last"></div>
    </div>
</div>


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
            <h3>New</h3>
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
                <h3>What's new?</h3>
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
