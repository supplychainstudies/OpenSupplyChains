<a href="user/<?= $user_id ?>"><?= ucwords(HTML::chars($username)) ?></a> updated 
<a href="view/<?= $supplychain_id ?>"><?= Text::limit_chars(isset($supplychain_title) && $supplychain_title ? HTML::chars($supplychain_title) : 'An Unnamed Sourcemap',40) ?></a>
