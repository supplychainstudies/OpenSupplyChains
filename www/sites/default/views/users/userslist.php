<h1>List of Users</h1>
<table border="1">
   <?$count = $offset + 1;?>

        <?php foreach ($users as $user): ?>
          <tr><td><?= $count; $count++;?><td><?= $user->username ?></td><td><?= $user->email ?></td></tr>
        <?php endforeach; ?>
</table>

<?php echo $page_links; ?>

    
