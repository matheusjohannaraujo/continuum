<?php 

$this->section("title", "Index");
$this->extends("layouts.html5");
$this->section("body");

?>
        <ul class="nav bg-primary border rounded p-2">
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= route("home"); ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= route("contact.new"); ?>">New Contact</a>
            </li>
        </ul>

    <?php
        if (count($contacts) > 0) {
    ?>
        <div class="table-responsive">
            <table class="table table-hover table-light border mt-3" style="border-radius: 7px;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Updated At</th>
                        <th scope="col">Show</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Destroy</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($contacts as $key => $contact) {
                    ?>
                        <tr>
                            <th scope="row"><?= $contact->id; ?></th>
                            <td><?= $contact->name; ?></td>
                            <td><?= $contact->email; ?></td>
                            <td><?= $contact->created_at; ?></td>
                            <td><?= $contact->updated_at; ?></td>
                            <td><a class="btn btn-primary" href="<?= route("contact.show", $contact->id); ?>">Link</a></td>
                            <td><a class="btn btn-secondary" href="<?= route("contact.edit", $contact->id); ?>">Link</a></td>
                            <td><a class="btn btn-danger" href="<?= route("contact.destroy", $contact->id) . "/?_method=DELETE&_csrf=" . csrf(); ?>">Link</a></td>
                        </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
            } else {
                echo "<div class=\"mt-3 alert alert-info fw-bold\" role=\"alert\">Contacts not found.</div>";
            }
        ?>

<?php $this->endSection(); ?>
