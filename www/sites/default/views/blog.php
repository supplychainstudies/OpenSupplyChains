<div id="blog-overview">
    <h1><?= HTML::chars($supplychain_name) ?></h1>
    <p class="description"><?= HTML::chars($supplychain_desc) ?></p>

    <p class="author">
        <img src="<?= $supplychain_avatar ?>" alt="Avatar"></img>
        <?= HTML::chars($supplychain_date) ?>, <a href="user/<?= HTML::chars($supplychain_ownerid) ?>"><?= HTML::chars($supplychain_owner) ?></a>
    </p>
</div>
<div id="blog-container">    

</div>

<div class="clear"></div>