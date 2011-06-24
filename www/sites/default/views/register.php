<?php if(!isset($current_user) || !$current_user): ?>

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


<?php else: ?>
<h2>You&apos;re logged in as <?= $current_user->username ?>.</h2>
<?php endif; ?>

