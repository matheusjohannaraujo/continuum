<?php 

$this->section("title", "New");
$this->extends("layouts.html5");
$this->section("body");

?>
        <ul class="mb-3 nav bg-dark rounded p-2">
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= route("home"); ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= route("contact.index"); ?>">Contacts</a>
            </li>
        </ul>

        <form class="mb-3 p-3 bg-dark text-white rounded" method="POST" action="<?= route("contact.create"); ?>">
            <?= tag_method("POST"); ?>
            <?= tag_csrf(); ?>
            <div class="form-group">
                <label for="input-name">Name:</label>
                <?= tag_message("name", ["class" => "alert alert-warning font-weight-bold", "role" => "alert"], "div"); ?>
                <input class="form-control" id="input-name" type="text" name="name" placeholder="Name" required value="<?= old("name"); ?>">
            </div>
            <div class="form-group">
                <label for="input-email">E-mail:</label>
                <?= tag_message("email", ["class" => "alert alert-warning font-weight-bold", "role" => "alert"], "div"); ?>
                <input class="form-control" id="input-email" type="email" name="email" placeholder="E-mail" required value="<?= old("email"); ?>">
            </div>
            <input class="btn btn-primary" type="submit" value="Save">
        </form>

<?php $this->endSection(); ?>
