<?php // Returns featured maps ?>
<div class="container dark-background">
    <div class="row featured">
        <ul id="slider">
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
            <li>
                <div class="map">
                    <img src="map/static/3.m.png" alt="" />
                    <div class="description">
                        <h2><a href="map/view/50">Featured Map Title</a><br /></h2>
                        <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
                        created by <a href="user/1">Alex</a> at 5:30pm
                    </div>
                </div>
            </li>
        </ul><!-- #slider -->
    </div><!-- .row -->
</div><!-- .container -->

<div class="spacer">
<div class="container">
    <div class="row">
        <div class="onecol"></div>
        <div class="tencol"></div>
            <h1>Sourcemap helps you understand where things come from.</h1>
            <p>Nam congue rutrum diam non malesuada. Mauris lobortis magna eget libero sollicitudin suscipit. In consectetur adipiscing ligula, nec ultricies mauris volutpat eget. </p>
            <p>Integer consectetur turpis eu orci convallis volutpat. Sed accumsan mattis urna et dictum. In blandit, sapien id cursus vehicula, nisi arcu convallis tortor, sed pharetra turpis orci interdum quam. Cras vitae est velit. Vestibulum commodo gravida orci non rhoncus. Aliquam consequat orci eget risus blandit vitae convallis urna blandit.</p>
            <p>Sed semper eros at urna sodales a tempus enim scelerisque. Aenean mauris lacus, ultricies a sodales eget, semper sed arcu. Aliquam diam ligula, hendrerit vel pretium ac, dapibus sed erat. </p>
        </div>
        <div class="onecol"></div>
    </div>
     
    <div class="row">

    

    <?php //todo: return the three most popular maps.  for now we're just pulling the first three. ?>
    <?php $i = 0;?>
    <?php foreach($supplychains as $id => $details): ?>
    <?php if ($i == 3){ break; } ?>
    <?php extract((array)$details); ?>
    <?php $title = isset($details->attributes->title) ? $details->attributes->title : 'Untitled'; ?>
        <div class="map-item fourcol<?php if ($i == 2){ ?> last<?php } ?>">
            <img src="map/static/<?= $id ?>.s.png" alt="" />
            <br />
            <a class="title" href="map/view/<?= $id ?>"><?= HTML::chars($title) ?></a>
            <br />
            <span>created by <a href="user/<?= $user_id ?>"><?= HTML::chars($owner->username) ?></a> at <?= date('H:ia', $created) ?></span>
        </div><!-- .map-item -->
    <?php $i++; ?>
    <?php endforeach; ?>
    </div><!-- .row -->
</div><!-- .container -->

<div class="spacer">
<div class="container">
    <div class="row">
        <div class="threecol">
            <h3>New</h3>
        </div>
        <div class="threecol">
            <h3>Popular</h3>
        </div>
        <div class="sixcol last">
            <h3>News</h3>
            <div class="news">
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
        </div><!-- .fourcol -->
    </div><!-- .row -->
</div><!-- .container -->

<div class="clear">&nbsp;</div>
