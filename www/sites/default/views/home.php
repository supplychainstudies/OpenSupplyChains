<div class="container_16">
    <div class="grid_8">
        <img src="<?= Gravatar::avatar($user->email, 128) ?>" />
        <h2><?= HTML::chars($user->username) ?></h2>
        <dl>
            <dt>Last Login:</dt><dd><?= date('F j, Y', $user->last_login) ?></dd>
        </dl>
    </div>
    <div class="grid_8 events">
        <h2>Recent Activity</h2>
        <?php if(isset($user_event_stream)): ?>
        <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
    <div class="grid_8">
        <?php if(isset($supplychains) && $supplychains): ?>
        <h2>Your maps</h2>
            <?php foreach($supplychains as $i => $sc): ?>
                <?= View::factory('partial/home/map', array('supplychain' =>$sc)) ?>
            <?php endforeach; ?>
        <?php else: ?>
        <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
        <?php endif; ?>
    </div>
</div>
