<!DOCTYPE html>
<html>
<head>
    <base href="<?= URL::base(true, true) ?>"></base>
<title>
||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||stonyfield demo
</title>
<style>
    body {
        font-family: sans-serif;
        background-color: #525b0c;
        padding: 1em;
    }
    #wrapper {
        width: 957px;
        margin: auto;
        overflow: hidden;
        background-color: #85951b;
    }
    #wrapper > div {
        margin: 0;
        padding: 0;
    }
    #head {
        width: 957px;
        height: 88px;
        background-image: url("sites/stonyfield/assets/images/stonyfield-demo-banner.png");
    }
    #content, #map {
        min-height: 400px;
        height: 400px;
        width: 100%;
        background-color: #fff;
    }
    #content > * {
        padding: 1em;
    }
    #foot {}
    #foot img {
        float: right;
        padding-right: 1em;
    }

    .clear {
        width: 100%;
        clear: both;
    }
</style>
</head>
<body>
<div id="wrapper">
    <div id="head">
    </div>
    <div id="content"><?= $content ?>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="clear">&nbsp;</div>
    <div id="foot">
        <a href="http://sourcemap.org"><img src="assets/images/logo-green.png" /></a>
        <div class="clear">&nbsp;</div>
    </div>
</div>
<?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
<script>
$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('map');
    var scids = [34,35,37,38];
    for(var i=0; i<scids.length; i++) { 
        var scid = scids[i];
        Sourcemap.loadSupplychain(scid, function(sc) {
            Sourcemap.map_instance.addSupplychain(sc);
        });
    }
});
</script>
</body>
</html>
