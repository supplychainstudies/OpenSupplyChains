<form name="edit-taxonomy" method="post" action="admin/taxonomy/<?= HTML::chars($term->id) ?>/edit">
<label for="title">Title:</label><br />
<input type="text" name="title" value="<?= HTML::chars($term->title) ?>" /><br />
<label for="name">Label (for urls, etc.):</label><br />
<input type="text" name="name" value="<?= HTML::chars($term->name) ?>" /><br />
<label for="description">Description:</label><br />
<textarea cols="40" rows="4" name="description"><?= HTML::chars($term->description) ?></textarea><br />
<input type="submit" value="Update" />

