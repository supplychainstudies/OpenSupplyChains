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

<div id="page-title">
    <div class="container">
        <h1>Renew your Subscription</h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">
        <p>Renew your Pro Account today and avoid a lapse in your account status.</p>
        <ul>
            <?= isset($thru) ? "<li>You've paid through " . date("F j, Y", $thru) . ".</li>" : ""?>
        </ul>
    </div>
    <div class="box-section upgrade">
    <?php if(isset($renew_form)): // this will happen if we have a seemingly valid credit card on file ?>
        <div class="sourcemap-form">
            <?= $renew_form ?>
        </div>
        <hr />
        <div class="container"><a class="expander" href="javascript:void(0)">Pay with a new credit card</a></div>
        <div class="sourcemap-form collapsed">
            <?= $form ?>
        </div>
    <?php else: ?>
        <div class="sourcemap-form">
            <?= $form ?>
        </div>
    <?php endif; ?>
    </div>
    <div class="clear"></div>
    <div class="credit-cards">
    </div>
    <div class="clear"></div>
</div>
