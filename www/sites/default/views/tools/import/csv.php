<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/ 
?>

<div class="container">
    <form class="sourcemap-form" name="csv-import" method="post" enctype="multipart/form-data">
    <label for="supplychain_name">Name:</label><br />
    <input name="supplychain_name" type="text" value="A Supply Chain" />

    <label for="stop_file">Stop File:</label><br /><br/>
    <input type="file" name="stop_file" /><br />
    <label for="hop_file">Hop File:</label><br /><br/>
    <input type="file" name="hop_file" /><br />
    <?php if(isset($user_supplychains) && $user_supplychains): ?>
    <br />
    <label for="replace-into">Create a New Map or Replace an Existing one?</label><br />
    <select name="replace_into">
    <option value="0">Create a new map</option>
    <?php foreach($user_supplychains as $sc): ?>
    <option value="<?= $sc->id ?>"><?= isset($sc->title) ? HTML::chars($sc->title) : 'Replace map ('.$sc->id.') created on '.date("F j, Y, g:i a", $sc->created) ?></option>
    <?php endforeach; ?>
    </select><br />
    <?php endif; ?>
    <input type="checkbox" name="publish" value="yes" checked="yes" /><label for="publish">Public</label><br />
    <input class="button" type="submit" value="Import" />
    </form>
</div>