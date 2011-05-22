<div class="clear"></div>
<div class="container">
    <div class="row">
        <?php // Return three featured items
        $data=Sourcemap_Search::Find(array('l'=>3));
        $results = $data->results;
        $i = 0;
        foreach($results as $item):
        ?>
            <div class="map-item fourcol<?php if($i == 2): ?> last<? endif; ?>">
            <img src="/map/static/<?php print $item->id; ?>.s.png" alt="" />
            <br />
            <?php print date("Y-m-d",$item->created);?>
            <br />
            </div>
            <?php $i++;
        endforeach;
        ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="container">
    <div class="row">
        <div class="fourcol">
            <?php // Return columns of category items
            $data=Sourcemap_Search::Find(array('l'=>3));
            $results = $data->results;
            $i = 0;
            foreach($results as $item):
            ?>
                <div class="map-item">
                <img src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
                <br />
                <?php print date("Y-m-d",$item->created);?>
                <br />
                </div>
                <?php $i++;
            endforeach;
            ?>
        </div><!-- .fourcol -->
        
        <div class="fourcol">
            <?php // Return columns of category items
            $data=Sourcemap_Search::Find(array('l'=>3));
            $results = $data->results;
            $i = 0;
            foreach($results as $item):
            ?>
                <div class="map-item">
                <img src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
                <br />
                <?php print date("Y-m-d",$item->created);?>
                <br />
                </div>
                <?php $i++;
            endforeach;
            ?>
        </div><!-- .fourcol -->
    </div><!-- .row -->
</div><!-- .container -->
