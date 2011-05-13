<!DOCTYPE html>  
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <base href="<?= URL::base(true, true) ?>"></base>
    <title><?= HTML::chars(isset($page_title) && $page_title ? $page_title : APPLONGNM) ?></title>

    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <?php if(isset($styles)): ?>
        <?= Sourcemap_CSS::link_tags($styles) ?>
    <?php endif; ?>
</head>
<body>
<?= isset($content) ? $content : '<h2>There\'s nothing here.</h2>' ?> 
</body>
</html>
