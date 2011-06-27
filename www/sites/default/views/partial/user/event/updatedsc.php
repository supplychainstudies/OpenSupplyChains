<a href="user/<?= $user_id ?>"><?= ucwords(HTML::chars($username)) ?></a> updated 
<a href="map/view/<?= $supplychain_id ?>"><?= isset($supplychain_title) && $supplychain_title ? HTML::chars($supplychain_title) : 'an unnamed Sourcemap' ?></a>
