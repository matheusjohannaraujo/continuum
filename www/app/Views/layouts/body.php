<body class="d-flex flex-column min-vh-100">
    
    <div class="container-fluid bg-white pt-3 pb-4" style="min-width: 300px;">

        <div class="container">

            <div class="p-5 text-center">
                <div class="mx-auto" style="max-width: 400px;">
                    <?= tag_img("logo-name.png", ["class" => "img-fluid"]); ?>
                </div>                
            </div>

            <?= $this->yield("body"); ?>

        </div>        

    </div>

    <footer class="mt-auto p-5" style="background: linear-gradient(180deg, #052C65 0%, #0A58CA 100%);">
        <div class="mx-auto" style="max-width: 300px;">
            <?= tag_img("logo-name-white.png", ["class" => "img-fluid"]); ?>
        </div>
    </footer>
</body>
</html>