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
        <h1>Manage your Payments</h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">
         <p>As a channel user, you have access to new modes, options, and features.</p>
         <ul>
             <li>Password-protected maps for private sharing</li>
             <li>The ability to create and save maps with Excel spreadsheets</li>
             <li>Enhanced API access</li>
             <li>...and more features coming soon!</li>
         </ul> 
    </div>
    <div class="box-section upgrade">
        <div class="sourcemap-form">
            <div class="container receipt">
                <h2 class="section-title">Card information for <?= isset($username) ? $username : "" ?></h2>
                <ul class="labels">
                    <li>Name on card:</li>
                    <li>Card type:</li>
                    <li>Card number:</li>
                    <li>Expires:</li>
                </ul>
                <ul>
                    <li><?= isset($card_name) ? $card_name : '';?></li>
                    <li><?= isset($card_type) ? $card_type: '';?></li>
                    <li><?= isset($card) ? $card : '';?></li>
                    <li><?= isset($exp_month) ? $exp_month : '';?> / <?= isset($exp_year) ? $exp_year : '';?></li>
                </ul>
                <h2 class="section-title">Account details</h2>
                <ul class="receipt labels">
                    <li>Joined:</li>
                    <li>Account level:</li>
                    <?= isset($thru) ? "<li>Paid through:</li>" : ""?>
                </ul>
                <ul class="receipt">
                    <li><?= date("F j, Y", $user->created); ?></li>
                    <li><?= isset($status) ? $status : ''; ?></li>
                    <?= isset($thru) ? "<li>" . date("F j, Y", $thru) . "</li>" : ""?>
                    <li>
                        <div class="button alternate">
                            <a href="/upgrade/renew">Renew Subscription</a>
                        </div>
                    </li>
                </ul>
                <div class="clear"></div>
                <hr class="spacer" />
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <h2 class="section-title">Your payments (<?= count($payments); ?>)</h2>
    <?php foreach($payments as $payment): ?>
        <div class="payment <?= $payment->closed == 1 ? "successful" : ""?>?>">
            <div class="container">
                <?= date("F j, Y", $payment->date); ?> -- 
                <?= $payment->closed == 1 ? "Processed" : "Processing"?> payment of <?= "$" . ($payment->total)/100; ?>
                with card xxxx xxxx xxxx <?= $payment->card->last4 ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
