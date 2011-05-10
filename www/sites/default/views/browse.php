<?php //Returns rows of three map-thumbs ?>
<?php foreach($supplychains as $id => $details): ?>

<?php if (($id % 3) == 0){  ?>
    <div class="row">
<?php } ?>

<?php extract((array)$details); ?>
<?php $title = isset($details->attributes->title) ? $details->attributes->title : 'Untitled'; ?>
<div class="map-thumb" style="background-image: url('map/static/<?= $id ?>');">
    <a href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a><br />
    created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?>
</div>

<?php if ((($id+2) % 3) == 0){ ?>
    </div><!-- .row -->
<?php } ?>

<?php endforeach; ?>
<div style="float: left; clear: both; width: 100%">&nbsp;</div>
