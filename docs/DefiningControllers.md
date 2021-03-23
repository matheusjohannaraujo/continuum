### Defining Controllers

> EN-US:

>> Contains the implementation of the controller's methods, with the possibility of generating automatic routes

>> Controller files must be created in the `app/Controllers` folder. The file name must be `ThingController.php`

>> Example: `app/Controllers/ContactController.php`

<hr>

### Definindo Controladores

> PT-BR:

>> Contém a implementação dos métodos do controlador, podendo ter geração de rotas automáticas

>> Os arquivos do controlador (ponte de entrado ao sistema) devem ser criados na pasta `app/Controllers`. O nome do arquivo deve ser `ThingController.php`

>> Exemplo: `app/Controllers/ContactController.php`
```php
<?php

namespace App\Controllers;

use App\Services\ContactService;

class ContactController
{

    private $contactService;
            
    public function __construct()
    {
        $this->contactService = new ContactService;
    }

    /*

        EN-US:

            Creating routes from the methods of a controller dynamically
            ------------------------------------------------------------------------------------------------
            This array below configures how the route works
            ------------------------------------------------------------------------------------------------
            array $CONFIG = [
                'method' => 'POST',
                'csrf' => false,
                'jwt' => false,
                'cache' => -1,
                'name' => 'test.create'
            ]
            ------------------------------------------------------------------------------------------------
            To use the route, it is necessary to inform the name of the Controller, the name of the Method 
            and the value of its parameters, the `array parameter $CONFIG` being only for configuration
            ------------------------------------------------------------------------------------------------
            Examples of use the routes:

                Controller = ContactController
                Method = action
                Call = ContactController@action(...params)
            ------------------------------------------------------------------------------------------------
                | HTTP Verb | ContactController@method   | PATH ROUTE
            ------------------------------------------------------------------------------------------------
                | GET       | ContactController@index     | /contact/index
                | GET       | ContactController@index_raw | /contact/index_raw
                | POST      | ContactController@create    | /contact/create
                | GET       | ContactController@new       | /contact/new
                | GET       | ContactController@edit      | /contact/edit/1
                | GET       | ContactController@show      | /contact/show/1
                | PUT       | ContactController@update    | /contact/update/1
                | DELETE    | ContactController@destroy   | /contact/destroy/1
            ------------------------------------------------------------------------------------------------

        PT-BR:

            Criando rotas a partir dos métodos de um controlador de forma dinâmica
            ------------------------------------------------------------------------------------------------
            Essa matriz abaixo configura como deve funcionar a rota de cada método
            ------------------------------------------------------------------------------------------------
            array $CONFIG = [
                'method' => 'POST',
                'csrf' => false,
                'jwt' => false,
                'cache' => -1,
                'name' => 'test.create'
            ]
            ------------------------------------------------------------------------------------------------
            Para usar a rota, é necessário informar o nome do Controlador, o nome do Método e o valor de
            seus parâmetros, sendo o `parâmetro da matriz $CONFIG` apenas para configuração
            ------------------------------------------------------------------------------------------------
            Exemplos de usa das rotas:

                Controller = ContactController
                Method = action
                Call = ContactController@action(...params)
            ------------------------------------------------------------------------------------------------
                | Verbo HTTP | ContactController@method  | Caminho da Rota
            ------------------------------------------------------------------------------------------------
                | GET       | ContactController@index     | /contact/index
                | GET       | ContactController@index_raw | /contact/index_raw
                | POST      | ContactController@create    | /contact/create
                | GET       | ContactController@new       | /contact/new
                | GET       | ContactController@edit      | /contact/edit/1
                | GET       | ContactController@show      | /contact/show/1
                | PUT       | ContactController@update    | /contact/update/1
                | DELETE    | ContactController@destroy   | /contact/destroy/1
            ------------------------------------------------------------------------------------------------
            
    */

    /*
        EN-US: This variable informs that the public methods of this controller must be automatically mapped in routes
        -
        PT-BR: Essa variável informa que os métodos públicos deste controlador devem ser mapeados automaticamente nas rotas
    */
    private $generateRoutes;

    /*
        EN-US: List all contact
        -
        PT-BR: Lista todos os contatos
    */
    public function index(array $CONFIG = ["method" => "GET"])
    {
        return view("contact/index", ["contacts" => $this->contactService->all()]);
    }

    /*
        EN-US: Returns all contacts as an array
        -
        PT-BR: Retorna todos os contatos como um array

    */
    public function index_raw(array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->all()->toArray();
    }

    /*
        EN-US: Create a single contact
        -
        PT-BR: Cria um único contato
    */
    public function create(array $CONFIG = ["method" => "POST", "csrf" => true])
    {
        $name = input_req("name");
        $email = input_req("email");
        $this->contactService->insert($name, $email);
        redirect()->action("contact.index");
    }

    /*
        EN-US: Redirects to the page that creates a single contact
        -
        PT-BR: Redireciona para a página que cria um único contato
    */
    public function new(array $CONFIG = ["method" => "GET"])
    {
        return view("contact/new");
    }

    /*
        EN-US: Redirects to the page that updates a single contact
        -
        PT-BR: Redireciona para a página que atualiza um único contato
    */
    public function edit(int $id, array $CONFIG = ["method" => "GET"])
    {
        return view("contact/edit", ["contact" => $this->contactService->findId($id)]);
    }   

    /*
        EN-US: Get single contact
        -
        PT-BR: Obtém um único contato
    */
    public function show(int $id, array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->findId($id)->toArray();
    }   

    /*
        EN-US: Update a single contact
        -
        PT-BR: Atualiza um único contato
    */
    public function update(int $id, array $CONFIG = ["method" => "PUT", "csrf" => true])
    {
        $name = input_req("name");
        $email = input_req("email");
        $this->contactService->update($id, $name, $email);
        redirect()->action("contact.index");
    }

    /*
        EN-US: Destroy a single contact
        -
        PT-BR: Excluí um único contato
    */
    public function destroy(int $id, array $CONFIG = ["method" => "DELETE", "csrf" => true])
    {
        $this->contactService->delete($id);
        redirect()->action("contact.index");
    }

}

```

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
