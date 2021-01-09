### Esquemas e Modelos

> Permite fazer a modelagem e acesso ao banco de dados

> Para trabalhar com o banco de dados, o Eloquent ORM é usado como uma dependência do projeto, o que torna possível mapear o modelo relacional em objetos

> Com isso, é possível usar as funcionalidades do Illuminate Database (mecanismo de banco de dados do Laravel)

> Os bancos de dados suportados pelo Eloquent ORM são: MySQL, MariaDB, PostgreSQL, SQLite e SQL Server

> Configure o banco de dados no arquivo `.env`

> As dependências necessárias para o Eloquent ORM são: `illuminate/events`, `illuminate/database` and `illuminate/support`

> Para instalar as dependências através do composer, use o comando: `composer db-require`

> Se você deseja desinstalar o Eloquent ORM, use o comando: `composer db-remove`

### Definindo Esquema

> O esquema é usado para estruturar a tabela no banco de dados

> Os arquivos de esquema devem ser armazenados na pasta `app/Schemas`. E o nome do arquivo deve ser `things_capsule.php`

> Exemplo: `app/Schemas/contacts_capsule.php`
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

### Definindo Modelo

> Os modelos são usados ​​para representar e armazenar os dados no banco de dados, onde cada modelo está diretamente associado a um Esquema.

> Os arquivos de modelo devem ser criados na pasta `app/Models`. O nome do arquivo deve ser `Thing.php`

> Exemplo: `app/Models/Contact.php`
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

### [Voltar para página anterior](./README.md)
