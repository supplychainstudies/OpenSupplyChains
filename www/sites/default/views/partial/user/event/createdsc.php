<p>
    <a href="user/<?= $user_id ?>"><?= ucwords(HTML::chars($username)) ?></a> made a new 
    <a href="map/view/<?= $supplychain_id ?>">Sourcemap</a><?= isset($supplychain_title) ? ' called <a href="map/view/'.$supplychain_id.'">'.HTML::chars($supplychain_title).'</a>' : ''?>.</a>
</p>
