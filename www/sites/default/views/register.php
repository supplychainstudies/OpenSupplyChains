<?php if(!isset($current_user) || !$current_user): ?>
		
<div class="container">
    <div class="register-copy">
        <h1>Join Us</h1>
        <p>Register to create sourcemaps, leave comments, save favorites, and stay informed about our work.</p><br/>
        <ul>
            <li>Already have an account? <a href="/auth">Sign in here</a>.</li>
        </ul>
    </div>
    <div class="register-box">
        <h1>&nbsp;</h1>
        <?php // Create form using the Sourcemap_Form class
        $f = Sourcemap_Form::factory('register')
            ->method('post')
            ->action('register');
        $f->input('email', 'Email', 1)
            ->input('username', 'Username', 2)
            ->password('password', 'Password', 3)
            ->password('password_confirm', 'Password (again)', 4)
			->textarea('terms', "Terms of Service", 5)
            ->submit('register', 'Register', 6);

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

<?php else: ?>
<h2>You&apos;re signed in as <?= $current_user->username ?>.</h2>
<?php endif; ?>