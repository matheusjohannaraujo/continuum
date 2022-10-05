<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->yield("title"); ?></title>
    <?= tag_favicon("index.jpeg", "jpeg"); ?>
    <link rel="canonical" href="<?= site_url(); ?>"/>
    <link rel="next" href="<?= site_url(); ?>">
    <meta name="description" content="MakeMVCSS - Matheus Johann Araújo">
    <meta name="keywords" content="MakeMVCSS - Matheus Johann Araújo. MakeMVCSS. Matheus Johann Araújo. Matheus. Johann. Araújo. Matheus Johann. Matheus Araújo">
    <meta name="robots" content="<?= site_url(); ?>">
    <meta name="author" content="Matheus Johann Araújo"/>
    <meta property="og:title" content="MakeMVCSS - Matheus Johann Araújo">
    <meta property="og:description" content="MakeMVCSS - Matheus Johann Araújo">
    <meta property="og:image" content="<?= folder_public("img/index.jpeg"); ?>"/>
    <meta property="og:url" content="<?= site_url(); ?>">
    <meta property="og:site_name" content="MakeMVCSS - Matheus Johann Araújo">
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
    
    <!-- Optional JavaScript; choose one of the two! -->
    <?= tag_js("index.js"); ?>
</head>
