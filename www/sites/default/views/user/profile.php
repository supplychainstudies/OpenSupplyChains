<div class="container_16">
    <div class="grid_8">
        <img src="<?= $user->avatar ?>" />
        <h1><?= HTML::chars($user->username) ?></h1>
        <dl>
            <dt>Last Login:</dt><dd><?= date('F j, Y', $user->last_login) ?></dd>
        </dl>
    </div>
    <div class="grid_8">
        <?php if($supplychains): ?>
        <h2>Public Maps</h2>
        <?php foreach($supplychains as $scid => $sc): ?>
            <div class="map-preview">
               <?= View::factory('partial/search/result', array('result' => (object)$sc)) ?> 
           </div>
        <?php endforeach; ?>
        <?php else: ?>
        <h2 class="bad-news">This user hasn't published any maps.</h2>
        <?php endif; ?>
    </div>
</div>
