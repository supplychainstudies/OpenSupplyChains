<?php foreach($supplychains as $id => $details): ?>
<?php extract((array)$details); ?>
<?php $title = isset($details->attributes->title) ? $details->attributes->title : 'A new Sourcemap'; ?>
<div class="map-thumb" style="border: .2em solid #ccc; background-position: -300px -300px; height: 10em; width: 20em; background-image: url('map/static/<?= $id ?>'); margin: 1em; float: left;">
<a href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a><br />
created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?>
</div>
<?php endforeach; ?>
<div style="float: left; clear: both; width: 100%">&nbsp;</div>
