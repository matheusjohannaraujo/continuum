<?php 

$title = "Edit";
$this->layout("layouts/html5");
$this->section("body");

?>
        <ul class="mb-3 nav bg-dark rounded p-2">
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("home"); ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("contact.index"); ?>">Contacts</a>
            </li>
        </ul>

        <form class="mb-3 p-3 bg-dark text-white rounded" method="POST" action="<?= action("contact.update", $contact->id); ?>">
            <?= tag_method("PUT"); ?>
            <?= tag_csrf(); ?>
            <div class="form-group">
                <label>Id:</label>
                <input class="form-control" type="number" name="id" placeholder="Id" readonly required value="<?= old("id", $contact->id); ?>">
            </div>
            <div class="form-group">
                <label for="input-name">Name:</label>
                <?= tag_message("name", ["class" => "alert alert-warning font-weight-bold", "role" => "alert"], "div"); ?>
                <input class="form-control" id="input-name" type="text" name="name" placeholder="Name" required value="<?= old("name", $contact->name); ?>">
            </div>
            <div class="form-group">
                <label for="input-email">E-mail:</label>
                <?= tag_message("email", ["class" => "alert alert-warning font-weight-bold", "role" => "alert"], "div"); ?>
                <input class="form-control" id="input-email" type="email" name="email" placeholder="E-mail" required value="<?= old("email", $contact->email); ?>">
            </div>
            <input class="btn btn-primary" type="submit" value="Save">
        </form>

<?php $this->endSection(); ?>
