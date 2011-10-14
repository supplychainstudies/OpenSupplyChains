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

$data=Sourcemap_Search::Find(array('l'=>4));
if ($data):
    $results = $data->results;
    $i = 0;
    foreach($results as $item):
        ?>
            <div class="preview-badge">
            <a href="view/<?php print $item->id; ?>">
                <img class="preview-map small" src="/map/static/<?php print $item->id; ?>.t.png" alt="" />
            </a>
            </div>
            <h3 class="preview-title">
                <a href="view/<?php print $item->id; ?>">
                <?= Text::limit_chars(HTML::chars($item->attributes->title), 16) ?>
                </a>
            </h3>
            <div class="preview-author">
                <h4>
                    <a href="user/<?php $item->owner->id; ?>">
                        <?= isset($item->owner->name) ? Text::limit_chars(HTML::chars($item->owner->name), 17) : "Unknown Author" ?></a>, 
                    <?php print date("M j, Y",$item->created);?>
                </h4>
            </div>
        </div>
    <?php $i++;
    endforeach;
else:
    print "<h2>No maps have been created yet.</h2>";
endif;
?>
