<p>Dear <?=HTML::chars($email_vars['username']); ?>,</p>
<p>Thank you for creating a Sourcemap account with us. 
Please click on the below link to confim your email address:<br />
     <?= HTML::anchor("register/confirm?u=".$email_vars['hash_value']) ?>
</p>
<p>- The Sourcemap Team</p>
