<?php 

$title = "Index";
$this->layout("layouts/html5");
$this->section("body");

?>
        <ul class="nav bg-dark rounded p-2">
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("home"); ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("contact.new"); ?>">New Contact</a>
            </li>
        </ul>

    <?php
        if (count($contacts) > 0) {
    ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-dark mt-3" style="border-radius: 7px;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Show</th>
                        <th>Edit</th>
                        <th>Destroy</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($contacts as $key => $contact) {
                    ?>
                        <tr>
                            <td><?= $contact->id; ?></td>
                            <td><?= $contact->name; ?></td>
                            <td><?= $contact->email; ?></td>
                            <td><?= $contact->created_at; ?></td>
                            <td><?= $contact->updated_at; ?></td>
                            <td><a class="btn btn-primary" href="<?= action("contact.show", $contact->id); ?>">Link</a></td>
                            <td><a class="btn btn-warning" href="<?= action("contact.edit", $contact->id); ?>">Link</a></td>
                            <td><a class="btn btn-danger" href="<?= action("contact.destroy", $contact->id) . "/?_method=DELETE&_csrf=" . csrf(); ?>">Link</a></td>
                        </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
            } else {
                echo "    <div class=\"mt-3 alert alert-info font-weight-bold\" role=\"alert\">
            Contacts not found.
        </div>";
            }
        ?>

<?php $this->endSection(); ?>
