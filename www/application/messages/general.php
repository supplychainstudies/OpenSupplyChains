<?php
/* Copyright (C) Sourcemap 2011 */

/* This is meant to be a comprehensive list of every message. */

return array(
    // General
    'testing' => 'Testing :number hooray!',
    'invalid-captcha' => 'Incorrect captcha.',
    'form-validation-fail' => 'Check the information below and try again.',
    'form-validation-errors' => 'Please correct the errors below.',
    'bad-request' => 'Bad Request',
    'no-permission' => 'You don\t have permission to do that.',

    // Email
    'email-from' => array('noreply@sourcemap.com' => 'The Sourcemap Team'),
    'headers' => array(
        'email' => 'hey',
        'two' => 'sup'
    ),

    // Map
    'map-doesnt-exist' => 'That map does not exist.',

    // Edit
    'edit-complete' => 'Map updated.',
    'edit-failed' => 'Couldn\t update your supplychain. Please contact support.',
    'edit-not-permitted' => 'You\'re not allowed to edit that map.',
    'edit-missing-parameter' => 'Missing required parameter',
    'edit-missing-publish' => 'Missing required "publish" parameter.',
    'edit-cant-private' => 'You are not allowed to private this map. Please contact support.',

    // Contact
    'contact-failed' => 'Sorry, could not send message. Please contact support.',

    // Registration
    'register-alpha-character' => 'Please use an alphabetical character as first letter of your username.',
    'register-restricted' => 'That username is restricted.  Please try a different username.',
    'register-taken' => 'That username is taken.',
    'register-email-exists' => 'An account exists for that email address.',
    'register-generic' => 'Could not complete registration. Please contact support.',
    'register-email-sent' => 'Activation email sent.',
    'register-already-in' => 'You\'re already signed in. Sign out and click the confirmation url again.',
    'register-token-expired' => 'That token has expired.',
    'register-invalid-token' => 'Invalid confirmation token.',
    'register-confirmed' => 'Your account has been confirmed. Please Sign in (and start mapping).',
    'register-email-subject' => 'Re: Your New Sourcemap Account',
    'register-email-body' => '
        Dear :user,
        
        Welcome to Sourcemap!
        
        Click the link below to activate your account:
        :url
        
        If you have any questions, please email support@sourcemap.com.
        -The Sourcemap Team',

    // Upgrade
    'upgrade-cc-failed' => 'Please check your credit card information and try again.',
    'upgrade-email-sent' => 'Email confirmation sent.',
    'upgrade-generic' => 'Sorry, could not complete account upgrade. Please contact support.',
    'upgrade-already-done' => 'You\'ve already upgraded your account.',
    'upgrade-havent-upgraded' => 'You haven\'t upgraded your account yet.',
    'upgrade-no-cc' => 'No credit card on file.  Please contact support.',
    'upgrade-lookup-failed' => 'There was a problem looking up your account.  Please contact support.',
    'upgrade-email-subject' => 'Re: Your Newly Upgraded Sourcemap Account',
    'upgrade-email-body' => '
        Dear :user,
        
        Thank you for upgrading to a Pro Account.
 
        As a Pro user, you will have access to exclusive feature that aren\'t available to the general public-- Most importantly, the ability to brand your account with custom colors, logos, and banners.  Before you start mapping with your upgraded account, we recommend you fill in the newly available fields in your dashboard.

        Once your payment is processed, you will receive an e-mail invoice for your records.
        
        If you have any questions, please contact us at proaccounts@sourcemap.com.

        -The Sourcemap Team',
    
    'upgrade-payment-email-subject' => 'Re: Your Newly Upgraded Sourcemap Account',
    'upgrade-payment-email-body' => '
        Dear :card-name,

        Your payment has been processed.  Please refer to the details below:

        ------------------------------------------------------------------------
        Payment information
        Total: :payment-amount
        Date: :payment-date

        Card information
        Name on card: :card-name
        Card type: :card-type
        Card number: :card-number
                
        Account details
        Username: :user
        Account level: :acct-level
        Paid through: :acct-paidthru
        ------------------------------------------------------------------------

        We appreciate your business. If you have any questions, please contact us at proaccounts@sourcemap.com.

        -The Sourcemap Team',
    
    // Pro Account Renewals
    'renew-email-subject' => 'Re: Your Pro Account Renewal',
    'renew-email-body' => '
        Dear :user,

        Thank you for renewing your Pro Account!
        
        As a Pro user, you will have access to exclusive feature that aren\'t available to the general public-- Most importantly, the ability to brand your account with custom colors, logos, and banners.  Before you start mapping with your upgraded account, we recommend you fill in the newly available fields in your dashboard.
        
        If you have any questions, please contact us at proaccounts@sourcemap.com.

        -The Sourcemap Team',

);
