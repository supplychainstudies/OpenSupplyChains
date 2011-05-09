<head>
<?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : Sourcemap_CSS::link_tags(
        'sites/default/assets/styles/reset.css',
        'sites/default/assets/styles/general.less'
    ) ?>
</head>

<?php foreach($supplychains as $id => $details): ?>
<?php extract((array)$details); ?>
<?php $title = isset($details->attributes->title) ? $details->attributes->title : 'Untitled'; ?>
<div class="map-thumb" style="background-image: url('map/static/<?= $id ?>');">
<a href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a><br />
created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?>
</div>
<?php endforeach; ?>
<div class="clear">&nbsp;</div>
