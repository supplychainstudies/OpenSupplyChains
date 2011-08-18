<div id="map-container">    
    <div id="map">
        <div id="sourcemap-map-view"></div>
    </div>
    
</div>
<div class="spacer"></div>
<div id="map-secondary" class="container">
    <div id="sidebar" class="map-view">
        <?php if($can_edit): ?>
			<div class="impact-box">
            	<input type="checkbox" <?= $supplychain_weight; ?> id="impact-use-weight" /> 
				<label for="impact-use-weight">Show Weight</label>
				<div class="clear"></div>
			</div>
			<div class="impact-box">
            	<input type="checkbox" <?= $supplychain_co2e; ?> id="impact-use-co2e" />
				<label for="impact-use-weight">Show Carbon Footprint (CO2e)</label>   
				<div class="clear"></div>
			</div>
            <hr />            
        <?php endif; ?>

        <div id="qrcode-badge" class="container">
            <a href="<?= $scaled_qrcode_url ?>"><img class="qrcode" src="<?= $qrcode_url ?>" /></a><br/>
        </div>
        <div class="container links">
            <p>Link to this Sourcemap</p>
            <div>
                <input value="<?= $short_link; ?>" readonly="readonly" onclick="select()"></input>
            </div>
            <p>Embed this Sourcemap</p>
            <div>
                <input value='<iframe width="640px" height="480px" frameborder="0" src="<?= URL::site(NULL, TRUE) ?>embed/<?= $supplychain_id ?>"></iframe>' onclick="select()" readonly="readonly"></input>
            </div>
        </div>
		<div class="clear"></div>
        <hr />
                
        <?= View::factory('partial/social', array('supplychain_id' => $supplychain_id)); ?>
    </div>
    

    <h1>
		<?= $supplychain_name ?> 
		<?php if($can_edit): ?>
        	<a id="map-edit-button" class="button" href="edit/<?= $supplychain_id; ?>">Edit</a>
    	<?php endif; ?>
	</h1>
    <p class="description"><?= $supplychain_desc ?></p>
    <hr />
    <p class="author">
        <img src="<?= $supplychain_avatar ?>" alt="Avatar"></img>
        <a class="author-link" href="user/<?= $supplychain_ownerid ?>"><?= $supplychain_owner ?></a>, <?= $supplychain_date ?>, Categorized as <?= $supplychain_cat; ?>.
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
        <p><a href="/auth">Sign in</a> or <a href="/register">register</a> to add to the discussion</p> 
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
