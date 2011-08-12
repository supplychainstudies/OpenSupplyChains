<a href="user/<?= $user_id ?>"><?= ucwords(HTML::chars($username)) ?></a> made a new 
<a href="map/view/<?= $supplychain_id ?>">sourcemap</a><?= isset($supplychain_title) ? ' called <a href="map/view/'.$supplychain_id.'">'.Text::limit_chars(HTML::chars($supplychain_title),30).'</a>' : ''?>.</a>
