<div class="container_16">
    <div class="grid_16">
        <div id="user-profile">
            <h2 class="user-name"><?= HTML::chars($user->username) ?></h2>       
            <img class="user-gravatar" src="<?= $user->avatar ?>" />            
            <ul class="user-details">
                <li>Last Login: <?= date('F j, Y', $user->last_login) ?><li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="container_16 search-results">
        <?php if(isset($supplychains) && $supplychains): ?>
            <h2><?= HTML::chars($user->username) ?>'s maps</h2>
            <?php foreach($supplychains as $i => $sc): ?>
                <div id="user-map-list" class="<?= $i % 2 ? " alt" : ''; ?>">
                    <?= View::factory('partial/user/map', array('supplychain' => $sc)) ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>

        <?php endif; ?>
</div>
