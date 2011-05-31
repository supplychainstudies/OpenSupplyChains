<?php // Returns featured maps ?>
<div class="clear"></div>
<div class="grids">
    <div class="grid grid-16 featured">
        <?= View::factory('partial/thumbs/slider', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
</div>

<div class="spacer"></div>

<div class="container_16">
    <div class="grid_16">
        <h1>Sourcemap is a platform for researching, optimizing and sharing supply chains.</h1>
        <p>Integer consectetur turpis eu orci convallis volutpat. Sed accumsan mattis urna et dictum. In blandit, sapien id cursus vehicula, nisi arcu convallis tortor, sed pharetra turpis orci interdum quam. Cras vitae est velit. Vestibulum commodo gravida orci non rhoncus. Aliquam consequat orci eget risus blandit vitae convallis urna blandit.</p>
    </div>
    <div class="grid_16">
        <h3>Popular Today</h3>
    </div>
</div>
    <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    
<div class="container_16">
    <div class="grid_3">
        <h3>Recent</h3>
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
    <div class="grid_6">
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
    </div>
</div><!-- .container_16 -->

<div class="clear">&nbsp;</div>
