<?php

$this->section("title", "Continuum Framework | Template: String View");
$this->extends("layouts.html5");
$this->section("body");

?>    

    <div class="container text-center">
        <h4 class="display-4">Template: String View</h4>
        <hr>
        <?php for ($i = 0; $i <= $size; $i++) { ?>
            <h5><?= $name . " - " . $i; ?></h5>
        <?php } ?>
    </div>

<?php $this->endSection(); ?>
