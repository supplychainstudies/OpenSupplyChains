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
        <h1>Thank you for upgrading!</h1>
    </div>
</div>

<div class="container form-page">
    <div class="copy-section">
        <p>Your account has been upgraded to a Channel.  You should see a confirmation in your inbox within the next few minutes.</p> 
        <ul>
            <li><a href="/home">Go back to your Dashboard</a></li>
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
                </ul>
                <div class="clear"></div>
                <hr class="spacer" />
            </div>
        </div>
    </div>
    <div class="clear"></div>

</div>
