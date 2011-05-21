
<div class="clear"></div>
<div class="container">
    <div class="row">
    <?php // Get featured stuff 
    $data=Sourcemap_Search::Find(array('l'=>3));
    $results = $data->results;
    $i = 0;
    foreach($results as $item){
    ?>
        <div class="map-item fourcol<?php if($i == 2): ?> last<? endif; ?>">
        <img src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
        <?php
        print "<br />";
        print date("Y-m-d",$item->created); 
        print "<br />";
        print "<br />";
        print "</div>";
        $i++;
    }
    ?>

    </div><!-- .row -->
</div><!-- .container -->
