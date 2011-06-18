<div class="container_16">
    <div class="grid_8">
        <div id="user-profile">
            <h2 class="user-name"><?= HTML::chars($user->username) ?></h2>       
            <img class="user-gravatar" src="<?= Gravatar::avatar($user->email, 128) ?>" />            
            <ul class="user-details">
                <li>Last Login: <?= date('F j, Y', $user->last_login) ?><li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
    <div class="grid_8 events">
        <div id="user-stream">
            <h2>Recent Activity</h2>
            <?php if(isset($user_event_stream)): ?>
            <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="container_16 search-results">
    <div class="grid_16">
        <?php if(isset($supplychains) && $supplychains): ?>
            <h2>Your maps</h2>
            <?php $count = 0; ?>
                <?php foreach($supplychains as $i => $sc): ?>
                <div id="user-map-list" class="container_16 <?php if ($count % 2 == 0 ) { echo " alt"; } ?>">
                    <?= View::factory('partial/search/result', array('result' => $sc)) ?>
                </div>
                <?php $count++; ?>
            <?php endforeach; ?>
            <?php else: ?>
            <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
        <?php endif; ?>
    </div>
</div>
