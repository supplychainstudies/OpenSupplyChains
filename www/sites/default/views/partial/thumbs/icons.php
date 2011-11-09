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

<?php if(isset($item->favorites_tot) && isset($item->comments_tot)): ?>
<div class="preview-icons">
    <?php if($item->favorites_tot > 0){ print '<div class="favorited color-' .  min(3, $item->favorites_tot) . '">' . $item->favorites_tot . '</div>'; } ?>
    <?php if($item->comments_tot > 0){ print '<div class="discussed color-' . min(3, $item->comments_tot) . '">' . $item->comments_tot . '</div>'; } ?>
</div>
<?php endif; ?>
