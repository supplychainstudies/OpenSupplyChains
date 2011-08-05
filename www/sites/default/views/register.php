<?php if(!isset($current_user) || !$current_user): ?>

<div class="container">
    <div class="register-copy">
        <h1>Register with Sourcemap</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet ultrices neque.</p>
        <p>In pulvinar ipsum quis orci ornare non lobortis turpis tincidunt. Integer at ligula erat, in hendrerit risus. Vivamus mattis</p>
        <p>Fusce ultricies pharetra eros, nec ornare magna auctor blandit. Fusce non risus non odio elementum mollis.</p>
        <div class="spacer">&nbsp;</div>
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


<?php else: ?>
<h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
<?php endif; ?>

