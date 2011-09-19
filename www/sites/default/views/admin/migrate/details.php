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
<?php if(isset($uid) && $uid): ?>
    <?php if(isset($oids) && $oids): ?>
        <fieldset><legend>Migrate All</legend>
        <form name="migrate_all" action="admin/migrate/" method="POST">
            <label for="old_user_id">Sourcemap.ORG User ID:</label><?= Form::input('old_user_id', $details[$oids[0]]->creator, array('disabled' => true)) ?>
            <label for="new_user_id">Sourcemap.COM User ID:</label><?= Form::input('new_user_id') ?>
            <input type="submit" value="Migrate Maps" />
        </form>
        </fieldset>
        <h2><?= count($oids) ?> Map<?= count($oids) > 1 ? 's' : '' ?> Available for Sourcemap.org User "<?= HTML::chars($uid) ?>"</h2>
        <table>
        <thead>
            <tr><th>OID</th><th>Title</th><th>Slug</th><th>Created</th>
        </thead>
        <tbody>
        <?php foreach($oids as $i => $oid): ?>
            <tr>
                <td><?= HTML::chars($oid) ?></td>
                <td>
                <?php if(isset($details[$oid])): ?>
                    <?= HTML::chars($details[$oid]->title) ?>
                <?php else: ?>
                    -
                <?php endif; ?>
                </td>
                <td>
                    <?= HTML::anchor($details[$oid]->oldurl, $details[$oid]->slug, array('target' => '_blank')) ?>
                </td>
                <td><?= HTML::chars($details[$oid]->created) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    <?php else: ?>
        <h3 class="bad-news">Nothing to migrate.</h3>
    <?php endif; ?>
<?php else: ?>
<h3 class="bad-news">No userid.</h3>
<?php endif; ?>
