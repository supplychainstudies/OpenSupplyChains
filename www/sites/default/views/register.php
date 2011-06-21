<?php if(!isset($current_user) || !$current_user): ?>
<div class="container_16">
<div class="grid_9">
<div class="sourcemap-form">
  <fieldset> 
    <legend>Log in</legend> 
    <form name="register" class="form" method="post" action="auth/login">
        <label for="textbox">Username</label>
        <div class="sourcemap-form-textbox"> 
            <input name="username" type="text" class="username" id="username" value="username" /> 
        </div> 
        <label for="password">Password</label> 
        <div class="sourcemap-form-textbox"> 
            <input name="password" type="password" class="password" id="password" value="password" /> 
        </div> 
        <?php if (isset($_GET['next'])): ?>
            <input type="hidden" name="next" value="<?= $_GET['next']; ?>" />
        <?php endif; ?>
        <div class="sourcemap-form-button-div"> 
            <input name="Submit" type="submit" value="Submit" class="buttons" /> 
        </div> 
    </form>
    </fieldset>
</div>
</div>
</div>

<?php else: ?>
<h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
<?php endif; ?>

<div class="container_16">
    <div class="grid_6">
    <h2>Register</h2>
        <?php // Create form using the Sourcemap_Form class
        $f = Sourcemap_Form::factory('register')
            ->method('post')
            ->action('register');

        $f->input('email', 'Email', 1)
            ->input('username', 'Username', 2)
            ->password('password', 'Password', 3)
            ->password('password_confirm', 'Password (again)', 4)
            ->submit('register', 'Register', 5);

        $f->field('email')->label('Email')
            ->add_class('email')
            ->add_class('required');
        $f->field('username')->label('Username')
            ->add_class('alphadash')
            ->add_class('required');
        $f->field('password')->label('Password')
            ->add_class('required');
        $f->field('password_confirm')->label('Password (again)')
            ->add_class('confirm')
            ->add_class('required');
        ?>
        <?= $f ?>
    </div>
</div>
