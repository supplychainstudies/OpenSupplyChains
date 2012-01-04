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
        <div class="sourcemap-form ajax vertical stripe">
            <fieldset>
                <form action="/upgrade" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded">
                    <label for="card-name">Name on Card</label> 
                    <input type="text" name="card-name" maxlength="140" class="textbox" />
                   
                    <div class="half-width" style="width: 245px">
                        <label for="card-number">Card Number</label>
                        <input type="text" name="card-number" maxlength="20" style="height: 28px; padding: 0" class="textbox" />
                    </div>

                    <div class="half-width" style="width: 110px">
                        <label for="card-cvc">CVC Code <a href="/legal/payment/#document-content?w=400" target="_blank" class="modal tooltip">?</a></label>
                        <input type="text" name="card-cvc" maxlength="5"  style="height: 28px; padding: 0" class="textbox" />
                    </div>
                    
                    <div class="clear"></div>

                    <div class="half-width">
                        <label for="card-expiry-month">Month</label>
                        <select name="card-expiry-month">
                            <?php 
                            $select = date("n");
                            for($i=1;$i<=12;$i++){
                                echo '<option value="' . $i;
                                if ($i == $select){ echo " SELECTED"; }
                                echo '">' . $i . '</option>';
                            } ?>
                        </select>
                    </div>

                    <div class="half-width">
                        <label for="card-expiry-year">Year</label>
                        <select name="card-expiry-year">
                            <?php 
                            $select = date("Y");
                            for($i=$select;$i<=$select+7;$i++){
                                echo '<option value="' . $i;
                                if ($i == $select){ echo " SELECTED"; }
                                echo '">' . $i . '</option>';
                            } ?>
                        </select>
                    </div>
                    
                    <div class="clear" style="height: 5px"></div>
                    
                    <label for="confirm_terms">I have read and agree to the <a href="/info/upgrade/#document-content?w=150" target="_blank" class="modal">terms of service</a>.</label> <input type="checkbox" name="confirm_terms" class="textbox" />

                    <div class="clear"></div>

                    <div class="submit-status hidden"></div>
                    
                    <input type="submit" name="upgrade" value="Upgrade" class="stripe button form-button" /> <input type="hidden" name="_form_id" value="upgrade" class=" textbox" />
                </form>
            </fieldset> 
        </div>
    </div>
    <div class="clear"></div>
    <div class="credit-cards">
    </div>
    <div class="clear"></div>
</div>
