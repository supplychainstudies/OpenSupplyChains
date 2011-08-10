<div id="map-container">    
    <div id="map">
        <div id="sourcemap-map-view"></div>
    </div>
    
</div>
<div class="spacer"></div>
<div id="map-secondary" class="container">
    <div id="sidebar" class="map-view">
        <?php if($can_edit): ?>
            <input type="checkbox" <?= $supplychain_weight; ?> id="impact-use-weight" /> Add Weight
            <input type="checkbox" <?= $supplychain_co2e; ?> id="impact-use-co2e" /> Add Carbon   
            <?php /*
            <input type="checkbox" <?= $supplychain_water; ?> id="impact-use-water" /> Add Water            
            */ ?>

            <hr />            
        <?php endif; ?>
        <div class="container">
            <a id="share-info"><h2>Share this Sourcemap</h2></a>
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
                <input value="<?= URL::site(NULL, TRUE) ?>view/<?= $supplychain_id ?>" readonly="readonly" onclick="select()"></input>
            </div>
            <p>Embed this Sourcemap</p>
            <div>
                <input value='<iframe width="640px" height="480px" frameborder="0" src="<?= URL::site(NULL, TRUE) ?>embed/<?= $supplychain_id ?>"></iframe>' onclick="select()" readonly="readonly"></input>
            </div>
        </div>
        <hr />
                
        <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
    </div>
    
    <?php if($can_edit): ?>
        <a href="edit/<?= $supplychain_id; ?>">Edit</a>
    <?php endif; ?>
    <h1><?= $supplychain_name ?></h1>
    <p class="description"><?= $supplychain_desc ?></p>
    
    <p class="author">
        <img src="<?= $supplychain_avatar ?>" alt="Avatar"></img>
        <?= $supplychain_date ?> : <a href="user/<?= $supplychain_ownerid ?>"><?= $supplychain_owner ?></a>
    </p>

    <div id="discussion-section">
        <h2>Discussion</h2>
        <?php if($can_comment): ?>
        <div id="comment-form" class="form">
            <fieldset>
            <form method="post" action="map/comment/<?= $supplychain_id ?>">
                <textarea placeholder="Type your comment..." name="body" id="comment-area"></textarea>
 
                <input class="button" id="comment-submit" type="submit" text="Comment"/>
                <div class="clear"></div>
            </form>
            </fieldset>
        </div>
       
        <?php else: ?>
        <p><a href="/auth">Log in</a> or <a href="/register">register</a> to add to the discussion</p> 
        <?php endif; ?>
        <?php if($comments): ?>
        <ul id="comments">
            <?php foreach($comments as $i => $comment): ?>
                <?= View::factory('partial/comment', array('comment' => $comment)) ?>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <h4 class="bad-news">Nobody's commented on this map yet.</h4>
        <?php endif; ?>
    </div><!-- #discussion-section -->
    
</div><!-- .container -->
<div class="clear"></div>
