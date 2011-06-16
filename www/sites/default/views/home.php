<div class="container_16">
    <div class="grid_6">
        <h2>Profile</h2>
    </div>
    <div class="grid_2">&nbsp;</div>
    <div class="grid_8">
        <h2>Recent Activity</h2>
    </div>
</div>

<div class="container_16">
    <div class="grid_3">
        <img src="<?= Gravatar::avatar($user->email, 128) ?>" />
    </div>
    <div class="grid_5">
        <h3><?= HTML::chars($user->username) ?></h3>
        Last Login: <?= date('F j, Y', $user->last_login) ?>
    </div>
    <div class="grid_8 events">
        <?php if(isset($user_event_stream)): ?>
        <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
        <?php endif; ?>
    </div>
</div>
<div class="clear"></div>

<div class="container_16 search-results">
    <div class="grid_16">
        <?php if(isset($supplychains) && $supplychains): ?>
            <h2>Your maps</h2>
            <?php $count = 0; ?>
                <?php foreach($supplychains as $i => $sc): ?>
                <div class="container_16 <?php if ($count % 2 == 0 ) { echo " alt"; } ?>">
                    <?= View::factory('partial/search/result', array('result' => $sc)) ?>
                </div>
                <?php $count++; ?>
            <?php endforeach; ?>
            <?php else: ?>
            <h2 class="bad-news">You haven't made any maps yet. <a href="create">Get started</a></h2>
        <?php endif; ?>
    </div>
</div>
