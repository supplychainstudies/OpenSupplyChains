<div class="row">
<?php foreach($supplychains as $id => $details): ?>
    
<?php extract((array)$details); ?>
<?php $title = isset($details->attributes->title) ? $details->attributes->title : 'Untitled'; ?>
    <div class="map-thumb threecol" style="background-image: url('map/static/<?= $id ?>');">
    <a href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a><br />
    created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?>
    </div>
<?php endforeach; ?>
</div><!-- .row -->
<div class="clear">&nbsp;</div>
