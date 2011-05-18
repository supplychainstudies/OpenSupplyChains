<?php // Returns featured maps ?>

<div class="row">
    <ul id="slider">
        <li>
            <div class="map">
                <img src="map/static/50" />
                <div class="description">
                    <a href="map/view/50">Featured Map Title</a><br />
                    created by <a href="user/1">Alex</a> at 5:30pm
                </div>
            </div>
        </li>
        <li>
            <div class="map">
                <img src="map/static/50" />
                <div class="description">
                    <a href="map/view/50">Featured Map Title</a><br />
                    created by <a href="user/1">Alex</a> at 5:30pm
                </div>
            </div>
        </li>
        <li>
            <div class="map">
                <img src="map/static/50" />
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
        <div class="map-item fourcol<?php if ((($id+2) % 3) == 0){ ?> last<?php } ?>">
            <img src="map/static/<?= $id ?>" /> 
            <a href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a><br />
            created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?>
        </div><!-- .map-item -->
<?php if ((($id+2) % 3) == 0){ ?>
    </div><!-- .row -->
<?php } ?>

<?php endforeach; ?>
<div class="clear">&nbsp;</div>
<div class="twitter">
    <ul>
        <li>
            <div style="background-image: url('http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg')">
            </div>
            <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
        </li>
        <li>
            <div style="background-image: url('http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg')">
            </div>
            <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
        </li>
        <li>
            <div style="background-image: url('http://s1.anscdn.net/resources/00000001084/8c868bdbe653f6e.jpg')">
            </div>
            <p>Phasellus aliquam tellus et orci scelerisque et aliquet libero posuere. Nunc ac tellus ac mauris blandit feugiat. Nulla at felis neque.</p>
        </li>
    </ul>
</div>
