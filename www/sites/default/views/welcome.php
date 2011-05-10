<?php // Returns featured maps ?>

<div class="row">
    <ul id="slider">
        <li>
            <div class="map" style="background-image: url('map/static/50');">
                <div class="description">
                    <a href="map/view/50">Featured Map Title</a><br />
                    created by <a href="user/1">Alex</a> at 5:30pm
                </div>
            </div>
        </li>
        <li>
            <div class="map" style="background-image: url('map/static/50');">
                <div class="description">
                    <a href="map/view/50">Featured Map Title</a><br />
                    created by <a href="user/1">Alex</a> at 5:30pm
                </div>
            </div>
        </li>
        <li>
            <div class="map" style="background-image: url('map/static/50');">
                <div class="description">
                    <a href="map/view/50">Featured Map Title</a><br />
                    created by <a href="user/1">Alex</a> at 5:30pm
                </div>
            </div>
        </li>
    </ul><!-- #slider -->
</div><!-- .row -->

<div class="row">
<h1>Sourcemap helps you understand where things come from.</h1>

<p>Nam congue rutrum diam non malesuada. Mauris lobortis magna eget libero sollicitudin suscipit. In consectetur adipiscing ligula, nec ultricies mauris volutpat eget. </p>
<p>Integer consectetur turpis eu orci convallis volutpat. Sed accumsan mattis urna et dictum. In blandit, sapien id cursus vehicula, nisi arcu convallis tortor, sed pharetra turpis orci interdum quam. Cras vitae est velit. Vestibulum commodo gravida orci non rhoncus. Aliquam consequat orci eget risus blandit vitae convallis urna blandit.</p>
<p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
</div>

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
