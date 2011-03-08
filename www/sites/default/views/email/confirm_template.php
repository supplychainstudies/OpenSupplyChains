<p>Dear <?=HTML::chars($email_vars['username']); ?>,</p>
<p>Thank you for creating a Sourcemap account with us. Please click on the below link to confim your email address:<br />
     http://localhost/smap/trunk/www/register/confirm?u=<?=$email_vars['hash_value']?></p>
<p>-Sourcemap Team</p>
