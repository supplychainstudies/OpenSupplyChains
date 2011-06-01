<div class="container_16">
    <div class="grid_8">
        <h2><?= HTML::chars($user['username']) ?></h2>
        <ul>
            <li>Last Login:</li>
            <li>Most recent edit:</li>
            <li>Page views:</li>
        </ul>
    </div>
    <div class="grid_8 events">
        <h2>What's new?</h2>
        <?php if(isset($user_event_stream)): ?>
        <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
    <div class="grid_8">
        <h2>Your maps</h2>
    </div>
</div>
