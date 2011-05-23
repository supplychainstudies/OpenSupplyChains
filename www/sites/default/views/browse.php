<div class="clear"></div>

<div class="container">
    <div class="row">
        <?= View::factory('partial/thumbs/featured', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="container">
    <div class="row">
        <div class="fourcol">
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        </div><!-- .fourcol -->
        
        <div class="fourcol">
            <h3>
            <?= View::factory('partial/thumbs/new', array('page_title' => isset($page_title) ? $page_title : APPLONGNM)) ?>
        
        <div class="fourcol last">
        </div><!-- .fourcol -->
    </div><!-- .row -->
</div><!-- .container -->
