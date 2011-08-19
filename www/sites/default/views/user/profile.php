<div class="container">
    <div class="dashboard-top">
        <div class="dashboard-top-left">
			<br/>
            <div id="user-profile">
                <div class="user-gravatar">
                    <img src="<?= $user->avatar ?>" />
                </div>
                <ul class="user-details">
	                <li><h2 class="user-name"><?= HTML::chars($user->username) ?></h2><li>
                    <li>Last Signed In: <span><?= date('F j, Y', $user->last_login) ?></span><li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
        <div class="dashboard-top-right">
            
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>

<div class="search-results container">
    <?php if(isset($supplychains) && $supplychains): ?><br/>
        <h2><?= HTML::chars($user->username) ?>'s Sourcemaps</h2>
        <div class="container pager">
            <?= $pager->render() ?>
        </div>
        <?php foreach($supplychains as $i => $sc): ?>
            <div class="user-map-list">
                <?= View::factory('partial/user/map', array('supplychain' => $sc)) ?>
            </div>
        <?php endforeach; ?>
        <div class="container pager">
            <?= $pager->render() ?>
        </div>
    <?php else: ?>
        <h2 class="bad-news">No maps yet!</h2>
    <?php endif; ?>
</div>
