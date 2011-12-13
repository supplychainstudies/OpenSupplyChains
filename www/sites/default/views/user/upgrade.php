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

<script type="text/javascript">
    Stripe.setPublishableKey('<?= Kohana::config('apis')->stripe_api_public_key ?>');
</script>

<div id="page-title">
    <div class="container">
        <h1>Upgrade your Account</h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">

    <p>Upgrade to a Sourcemap Channel for $99/year. You'll get:</p>
        <ul>
            <li>Password-protected maps for private sharing</li>
            <li>The ability to create and save maps with Excel spreadsheets</li>
            <li>Enhanced API access</li>
            <li>...and more features coming soon!</li>
        </ul>

    </div>
    <div class="box-section upgrade">
        <div class="sourcemap-form ajax stripe">
            <?= $form ?>
        </div>
    </div>
    <div class="clear"></div>
    <div class="credit-cards">
    </div>
    <div class="clear"></div>
</div>
