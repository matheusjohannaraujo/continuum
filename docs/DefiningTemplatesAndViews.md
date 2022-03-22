### Defining Templates and Views

> EN-US:

>> It is used to create screens, which can be static or dynamic. These screens can be used as `View` or `Template`

>> Controller files must be created in the `app/Views` folder. The file name must be `thing.php`

>> Use `AVR (Auto View Route)` to create a route that automatically leads to `View`, write the file name as `avr-thing.php`

>> Example: `app/Views/contact/new.php`

<hr>

### Definindo Templates e Visões

> PT-BR:

>> É utilizado para criação de telas, podendo ser estáticas ou dinâmicas. Essas telas podem ser usadas como `View` (Visão) ou `Template` (Modelo)

>> Os arquivos de visão devem ser criados na pasta `app/Views`. O nome do arquivo deve ser `thing.php`

>> Utilize o `AVR (Auto View Route)` para criar uma rota que leva automaticamente a `View`, escreva o nome do arquivo como `avr-thing.php`

>> Exemplo: `app/Views/contact/new.php`

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New</title>
    <?= tag_js("jquery-3.5.1.min.js"); ?>
    <?= tag_js("popper.min.js"); ?>
    <?= tag_css("bootstrap.min.css"); ?>
    <?= tag_js("bootstrap.min.js"); ?>
    <?= tag_js("bootstrap.bundle.min.js"); ?>
    <?= tag_js("index.js"); ?>
</head>
<body class="bg-dark m-3">
    <div class="container bg-light pt-3 pb-1 rounded" style="min-width: 300px;">

        <div class="jumbotron jumbotron-fluid bg-dark text-white p-2 pt-3 rounded mb-3 overflow-auto">
            <div class="container">
                <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank"><h1 class="text-primary" style="font-weight: 300; font-size: 2.7em;">MakeMVCSS</h1></a>
                <p class="lead text-wrap">A simple and complete PHP Framework, thought and designed for developers.</p>
            </div>
        </div>

        <ul class="mb-3 nav bg-dark rounded p-2">
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("home"); ?>">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="<?= action("contact.index"); ?>">Contacts</a>
            </li>
        </ul>

        <form class="mb-3 p-3 bg-dark text-white rounded" method="POST" action="<?= action("contact.create"); ?>">
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
            <input class="btn btn-primary" type="submit" value="Send and save">
        </form>

    </div>
</body>
</html>
```

> Example: `app/Views/contact/edit.php`
```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <?= tag_js("jquery-3.5.1.min.js"); ?>
    <?= tag_js("popper.min.js"); ?>
    <?= tag_css("bootstrap.min.css"); ?>
    <?= tag_js("bootstrap.min.js"); ?>
    <?= tag_js("bootstrap.bundle.min.js"); ?>
    <?= tag_js("index.js"); ?>
</head>
<body class="bg-dark m-3">
    <div class="container bg-light pt-3 pb-1 rounded" style="min-width: 300px;">

        <div class="jumbotron jumbotron-fluid bg-dark text-white p-2 pt-3 rounded mb-3 overflow-auto">
            <div class="container">
                <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank"><h1 class="text-primary" style="font-weight: 300; font-size: 2.7em;">MakeMVCSS</h1></a>
                <p class="lead text-wrap">A simple and complete PHP Framework, thought and designed for developers.</p>
            </div>
        </div>

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
                <label>Id</label>
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
            <input class="btn btn-primary" type="submit" value="Send and save">
        </form>

    </div>
</body>
</html>
```

> Example: `app/Views/contact/index.php`
```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <?= tag_js("jquery-3.5.1.min.js"); ?>
    <?= tag_js("popper.min.js"); ?>
    <?= tag_css("bootstrap.min.css"); ?>
    <?= tag_js("bootstrap.min.js"); ?>
    <?= tag_js("bootstrap.bundle.min.js"); ?>
</head>
<body class="bg-dark m-3">
    <div class="container bg-light pt-3 pb-1 rounded" style="min-width: 300px;">

        <div class="jumbotron jumbotron-fluid bg-dark text-white p-2 pt-3 rounded mb-3 overflow-auto">
            <div class="container">
                <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank"><h1 class="text-primary" style="font-weight: 300; font-size: 2.7em;">MakeMVCSS</h1></a>
                <p class="lead text-wrap">A simple and complete PHP Framework, thought and designed for developers.</p>
            </div>
        </div>

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
            echo "<div class=\"mt-3 alert alert-info font-weight-bold\" role=\"alert\">
                Contacts not found.
            </div>\r\n";
        }
    ?>    
    </div>
</body>
</html>
```

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
