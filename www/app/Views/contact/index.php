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

    <?php if (count($contacts) > 0) { ?>
        <div class="border border-primary bg-light rounded p-0 mt-3 mb-3">
            <div class="table-responsive">
                <table class="table table-hover table-light table-borderless">                    
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">UUID</th>
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
                                <td><?= $contact->uuid; ?></td>
                                <td><?= $contact->name; ?></td>
                                <td><?= $contact->email; ?></td>
                                <td><?= $contact->created_at; ?></td>
                                <td><?= $contact->updated_at; ?></td>
                                <td><a href="<?= route("contact.show", $contact->uuid); ?>"><i class="fa-solid fa-link text-info"></i></a></td>
                                <td><a href="<?= route("contact.edit", $contact->uuid); ?>"><i class="fa-solid fa-pen text-primary"></i></a></td>
                                <td><a href="<?= route("contact.destroy", $contact->uuid) . "/?_method=DELETE&_csrf=" . csrf(); ?>"><i class="fa-solid fa-trash-can text-danger"></i></a></td>
                            </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="mt-3 alert alert-info fw-bold" role="alert">Contacts not found.</div>
    <?php } ?>

<?php $this->endSection(); ?>
