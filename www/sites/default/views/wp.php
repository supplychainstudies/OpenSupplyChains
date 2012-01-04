<?php
/* Copyright (C) Sourcemap 2011 */
?>

<div id="page-title">
    <div class="container">
        <h1><?= isset($title) ? $title : "" ?></h1>
    </div>
</div>

<div class="container info">
    <ul id="toc"></ul>
    <div id="document-content">
        <?= $content ?>
    </div>
</div>
