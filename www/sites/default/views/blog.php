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

<div id="blog-overview">
    <h1><?= HTML::chars($supplychain_name) ?></h1>
    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>

    <p class="author">
        <img src="<?= $supplychain_avatar ?>" alt="Avatar"></img>
        <?= HTML::chars($supplychain_date) ?>, <a href="user/<?= HTML::chars($supplychain_ownerid) ?>"><?= HTML::chars($supplychain_owner) ?></a>
    </p>
</div>
<div id="blog-container">    

</div>

<div class="clear"></div>