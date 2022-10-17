### Defining Services

> EN-US:

>> It is used to make the application's business rule, such as data validations sent to the controller

>> Controller files must be created in the `app/Services` folder. The file name must be `ThingService.php`

>> Example: `app/Services/ContactService.php`

<hr>

### Definindo Serviços

> PT-BR:

>> É utilizado para fazer a lógica da regra de negócios da aplicação, como validações de dados submetidos ao controlador

>> Os arquivos do controlador de serviço (regras de negócio) devem ser criados na pasta `app/Services`. O nome do arquivo deve ser `ThingService.php`

>> Exemplo: `app/Services/ContactService.php`

```php
<?php

namespace App\Services;

use App\Models\Contact;

class ContactService
{

    private $contact;

    public function __construct()
    {
        $this->contact = new Contact();
    }

    public function all()
    {
        return $this->contact::orderBy('id', 'ASC')->get();
    }

    public function findId(int $id)
    {
        return $this->contact->find($id);
    }

    public function delete(int $id)
    {
        $this->contact = $this->contact->find($id);
        return $this->contact->delete();
    }  

    private function validateNameEmail(string $name, string $email)
    {
        $back = false;
        if (empty($name) || strlen($name) < 3) {
            $back = true;
            message("name", "Please enter a 'NAME' of at least 3 characters");
        }
        if (empty($email)) {
            $back = true;
            message("email", "Enter a valid 'EMAIL' address");
        }
        if ($back) {
            redirect()->withInput()->back();
        }
        return !$back;
    }

    public function insert(string $name, string $email)
    {
        $this->validateNameEmail($name, $email);
        $this->contact->name = $name;
        $this->contact->email = $email;
        $this->contact->save();
    }

    public function update(int $id, string $name, string $email)
    {
        $this->validateNameEmail($name, $email);
        $this->contact = $this->contact->find($id);
        $this->contact->name = $name;
        $this->contact->email = $email;
        $this->contact->save();
    }

}
```

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
