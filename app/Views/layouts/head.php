<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->yield("title"); ?></title>
    <?= tag_favicon("favicon.jpg", "jpg"); ?>
    <link rel="canonical" href="<?= site_url(); ?>"/>
    <link rel="next" href="<?= site_url(); ?>">
    <meta name="description" content="Continuum Framework">
    <meta name="keywords" content="Continuum Framework. Continuum. Framework. Continuum Framework - Matheus Johann Araújo. Continuum Framework Matheus. Continuum Framework Johann. Matheus Johann Araújo. Matheus Johann. Matheus Araújo. Matheus. Johann. Araújo.">
    <meta name="robots" content="<?= site_url(); ?>">
    <meta name="author" content="Matheus Johann Araújo"/>
    <meta property="og:title" content="Continuum Framework">
    <meta property="og:description" content="Continuum Framework">
    <meta property="og:image" content="<?= folder_public("img/favicon.jpg"); ?>"/>
    <meta property="og:url" content="<?= site_url(); ?>">
    <meta property="og:site_name" content="Continuum Framework">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="article">

    <!-- Bootstrap CSS -->
    <?= tag_css("bootstrap.min.css"); ?>

    <!-- jQuery -->
    <?= tag_js("jquery-3.6.1.min.js"); ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <?= tag_js("bootstrap.bundle.min.js"); ?>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <?= tag_js("popper.min.js"); ?>
    <?= tag_js("bootstrap.min.js"); ?>
    -->
    
    <?= tag_css("index.css"); ?>
    <?= tag_js("index.js"); ?>    
</head>
