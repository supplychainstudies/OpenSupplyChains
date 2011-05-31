<div class="container">
    <div class="row">
        <div class="sevencol"></div>
        <div class="fivecol last">
            <h2><?= HTML::chars($user['username']) ?></h2>
            <?php if(isset($user_event_stream)): ?>
            <h3>Recent Activity</h3>
            <?= View::factory('partial/user/event/stream', array('stream' => $user_event_stream)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
