<div class="container_16">
    <div class="grid_8">
        <img src="<?= $user->avatar ?>" />
        <h2><?= HTML::chars($user->username) ?></h2>
        <dl>
            <dt>Last Login:</dt><dd><?= date('F j, Y', $user->last_login) ?></dd>
        </dl>
    </div>
</div>
