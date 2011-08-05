<div class="container">
    <h1 class="dashboard-title">Your sourcemap profile</h1>
    <div class="dashboard-top">
        <div class="dashboard-top-left">
            <div>
                <h2 class="user-name"><?= HTML::chars($user->username) ?></h2>       
            </div>
            <hr />
            <div id="user-profile">
                <div class="user-gravatar">
                    <img src="<?= Gravatar::avatar($user->email, 128) ?>" />
                </div>
                <ul class="user-details">
                    <li>Last Login <span><?= date('F j, Y', $user->last_login) ?></span><li>
                </ul>
                <div class="clear"></div>
                <div class="upload-photo button">Upload photo</div>
            </div>
        </div>
        <div class="dashboard-top-right">
            <div>
                <h2>Recent Activity</h2>
            </div>
            <hr />
            <div id="user-stream">
                <?php if(isset($user_event_stream)): ?>
                <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>

<div class="search-results container">
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
