<div class="clear"></div>
<div class="container">
    <h3>Viewing all categories</h3>
    <div class="row">
        <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="clear"></div>
<div class="container">
    <div class="row">
        <div class="fourcol">
            <h3>Interesting:</h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        </div><!-- .fourcol -->
        
        <div class="fourcol">
            <h3>New:</h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        
        <div class="fourcol last">
        </div><!-- .fourcol -->
    </div><!-- .row -->
</div><!-- .container -->
