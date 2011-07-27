<div id="map-container">    
    <div id="map">
        <div id="sourcemap-map-view"></div>
    </div>
    
</div>
<div class="spacer"></div>
<div class="container">
    <div id="sidebar" class="map-view">
        <div class="container">
            <h2>Share this Sourcemap</h2>
        </div>
        <hr />
        <div id="qrcode-badge" class="container">
            <img class="qrcode" src="<?= $qrcode_url ?>" />
            <div class="qrcode-about">
                <p>You can share this physically with a qrcode.</p>
                <p><em>Print the QR code, or use a QR enabled smart phone to return to this map.</em></p>
            </div>
            <div class="clear"></div>
        </div>
        <hr />
        <div class="container links">
            <p>Link to this Sourcemap</p>
            <div>
                <input value="<?= URL::site(NULL, TRUE) ?>view/<?= $supplychain_id ?>" readonly="readonly"></input>
            </div>
            <p>Embed this Sourcemap</p>
            <div>
                <input value="<?= URL::site(NULL, TRUE) ?>embed/<?= $supplychain_id ?>" readonly="readonly"></input>
            </div>
        </div>
        <hr />
                
        <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
        
    </div>
    
    <h1><?= $supplychain_name ?></h1>
    <p class="description"><?= $supplychain_desc ?></p>
    
    <p class="author">
        <img src="<?= $supplychain_avatar ?>" alt="Avatar"></img>
        Created on <?= $supplychain_date ?> by <a href="user/<?= $supplychain_ownerid ?>"><?= $supplychain_owner ?></a>
    </p>

    <div id="discussion-section">
        <h2>Discussion</h2>
        <?php if($can_comment): ?>
        <div id="comment-form" class="form">
            <form method="post" action="map/comment/<?= $supplychain_id ?>">
                <textarea placeholder="Submit your comment." name="body" id="comment-area"></textarea>
                <input id="comment-submit" type="submit" text="Comment"/>
            </form>
            </fieldset>
        </div>
        
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
        <h4 class="bad-news">Nobody's commented on this map yet.</h4>
        <?php endif; ?>
    </div><!-- #discussion-section -->
    
</div><!-- .container -->
<div class="clear"></div>
