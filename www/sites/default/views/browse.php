<div class="clear"></div>
<div class="container_16">
    <div class="grid_16">
        <h2>Viewing all categories</h2>
    </div>
    <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
</div><!-- .container -->

<div class="clear"></div>
<div class="container_16">
    <div class="grid_4">
        <h2>Interesting:</h2>
        <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    
    <div class="grid_4">
        <h2>New:</h2>
        <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div>
    
    <div class="grid_4">
    </div>
</div><!-- .container -->
