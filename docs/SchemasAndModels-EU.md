### Schemas and Models

> Allows you to model and access the database

> To work with the database, the Eloquent ORM is used as a project dependency, which makes it possible to map the relational model in objects

> With that, it is possible to use the Illuminate Database functionalities (Laravel database engine)

> The databases supported by Eloquent ORM are: MySQL, MariaDB, PostgreSQL, SQLite and SQL Server

> Configure the database in the `.env` file

> The necessary dependencies for Eloquent ORM are: `illuminate/events`, `illuminate/database` and `illuminate/support`

> To install the dependencies through composer, use the command: `composer db-require`

> If you want to uninstall Eloquent ORM, use the command: `composer db-remove`

### Defining Schemas

> The schema is used to structure the table in the database

> The schema files must be stored in the `app/Schemas` folder. And the file name must be `things_capsule.php`

> Example: `app/Schemas/contacts_capsule.php`
```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('contacts');

Capsule::schema()->create('contacts', function ($table) {

    $table->increments('id');

    $table->string('name');

    $table->string('email')->unique();

    $table->timestamps();

});

```

### Defining Models

> The models are used to represent and store the data in the database, where each model is directly associated with a Schema

> The model files must be created in the `app/Models` folder. The file name must be `Thing.php`

> Example: `app/Models/Contact.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Contact extends Eloquent
{

    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email'
    ];

}

```

### [Back to the previous page](./README-EU.md)
