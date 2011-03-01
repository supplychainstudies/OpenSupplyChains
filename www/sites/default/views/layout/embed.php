<!doctype html>  

<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <base href="<?= URL::base(true, true) ?>"></base>
    <title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>

    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>

    </head>
    

</head>

<body id="supplychain" style="height: 100%; width: 100%; margin: 0; padding: 0;">
<?= $content ?>
</body>

    <?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
  
  <!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script> DD_belatedPNG.fix('img, .png_bg'); 
  <![endif]-->
  
</body>
<script>
    Sourcemap.embed_supplychain_id = <?= isset($supplychain_id) ? $supplychain_id : '"null"' ?>;
    Sourcemap.embed_params = <?= isset($embed_params) ? json_encode($embed_params) : '{}' ?>;
</script>
</html>
