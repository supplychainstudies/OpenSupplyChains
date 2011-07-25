<div class="container">
    <div id="user-profile">
        <h2 class="user-name"><?= HTML::chars($user->username) ?></h2>       
        <img class="user-gravatar" src="<?= Gravatar::avatar($user->email, 128) ?>" />            
        <ul class="user-details">
            <li>Last Login: <?= date('F j, Y', $user->last_login) ?><li>
        </ul>
        <div class="clear"></div>
    </div>
    <div class="events">
        <div id="user-stream">
            <h2>Recent Activity</h2>
            <?php if(isset($user_event_stream)): ?>
            <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="container search-results">
    <?php if(isset($supplychains) && $supplychains): ?>
        <h2>Your maps</h2>
        <?php foreach($supplychains as $i => $sc): ?>
            <div id="user-map-list" class="<?= $i % 2 ? " alt" : ''; ?>">
                <?= View::factory('partial/home/map', array('supplychain' => $sc)) ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
    <?php endif; ?>
</div>
