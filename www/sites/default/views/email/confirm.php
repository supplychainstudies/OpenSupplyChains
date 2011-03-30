Dear <?=HTML::chars($email_vars['username']); ?>,
 Thank you for creating a Sourcemap account with us. 
Please click on the below link to confim your email address:
<?= HTML::anchor("register/confirm?u=".$email_vars['hash_value']) ?>

The Sourcemap Team
