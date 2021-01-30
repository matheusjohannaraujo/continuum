<?php session()->set("lang", "en-us"); ?>
<body class="bg-dark m-3">
    <div class="container bg-light pt-3 pb-1 rounded" style="min-width: 300px;">
  
        <div class="jumbotron jumbotron-fluid bg-dark text-white p-2 pt-3 rounded mb-3 overflow-auto">
            <div class="container">
                <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank"><h1 class="text-primary" style="font-weight: 300; font-size: 2.7em;">MakeMVCSS</h1></a>
                <p class="lead text-wrap"><?= I18N_session("slogan"); ?></p>
            </div>
        </div>
<?= $this->renderSection("body"); ?>
    </div>
</body>
</html>