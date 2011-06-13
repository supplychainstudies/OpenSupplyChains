<div id="map">
    <div id="sourcemap-map-view" style="width: 100%; height: 100%; background-color: #ddd;"></div>
</div>
    <div class="clear"></div>

    <div class="container_16">
        <div class="grid_10"> 
            <div id="map-secondary-content">
                <div id="discussion-section">
                    <h3>Discussion</h3>
                    <?php if($can_comment): ?>
                    <div id="comment-form" class="form">
                        <fieldset><legend>Comment</legend>
                        <form method="post" action="map/comment/<?= $supplychain_id ?>">
                            <textarea name="body" id="comment-area"></textarea>
                            <input id="comment-submit" type="submit" text="Comment"/>
                        </form>
                        </fieldset>
                    </div>
                    <div class="clear"></div>
                    <?php endif; ?>
                    <?php if($comments): ?>
                    <ul id="comments">
                        <?php foreach($comments as $i => $comment): ?>
                            <li class="comment">
                            <?= View::factory('partial/comment', array('comment' => $comment)) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <h4 class="bad-news">Nobody's commented on this map yet. Be the first!</h4>
                    <?php endif; ?>
                </div><!-- #discussion-section -->
            </div><!-- #map-secondary-content -->
        </div><!-- .grid_10 -->
        <div class="grid_6"> 
            <div id="qrcode-badge">
                <img class="qrcode" src="<?= $qrcode_url ?>" />
                <div class="qrcode-about">You can share this physically with a qrcode.</div>
                <div class="clear"></div>
            </div>
            <!--h3>Similar Supplychains</h3>
            <ul id="similar-supplychains">
                <li><a href="">Ikea Product Map</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
                <li><a href="">Ikea Product Map</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
            </ul>
            <h3>Related Geographies</h3>
            <ul id="related-geographies">
                <li><a href="">Ikea Product Map</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
                <li><a href="">Ikea Product Map</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
                <li><a href="">Ikea Product Map with details.</a></li>
            </ul-->
        </div><!-- .grid_6 -->
    </div><!-- .container -->
    <div class="clear"></div>
</div>
