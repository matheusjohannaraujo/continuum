<body class="bg-dark m-3">
    <div class="container bg-white pt-3 pb-1 border rounded" style="min-width: 300px;">

        <div class="jumbotron jumbotron-fluid bg-primary p-2 pt-3 border rounded mb-3 overflow-auto">
            <div class="container">
                <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank"><h1 class="text-white" style="font-weight: 300; font-size: 2.7em;">MakeMVCSS</h1></a>
                <p class="lead fst-italic text-wrap text-white">"<?= I18N_session("slogan"); ?>"</p>
            </div>
        </div>
<?= $this->yield("body"); ?>
    </div>
</body>
</html>