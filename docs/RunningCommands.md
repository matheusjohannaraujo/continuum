### Running Commands

> EN-US: Inside the `Continuum` project folder there is a file called `adm`, with which it is possible to execute commands that perform some actions

> EN-US: The `adm` file allows you to generate Controllers, Views, Services, Models, Helpers and many other things

### Executando Comandos

> PT-BR: Dentro da pasta do projeto `Continuum` existe um arquivo chamado `adm`, com o qual é possível executar comandos que realizam algumas ações

> PT-BR: O arquivo `adm` permite a gerar Controladores, Visões, Serviços, Modelos, Ajudantes e muitas outras coisas

<hr>

> ##### EN-US: List all commands

> ##### PT-BR: Lista todos os comandos

>> **```php adm help```**

<hr>

> ##### EN-US: Clears the project, leaving only the default settings

> ##### PT-BR: Limpa o projeto, deixando apenas as configurações padrão

>> **```php adm clean```**

<hr>

> ##### EN-US: Start a web server on port 80

> ##### PT-BR: Inicia um servidor web na porta 80

>> **```php adm server```** , **```php adm server:80```**

<hr>

> ##### EN-US: Creates a controller file inside the folder "app/Controllers/TestController.php"

> ##### PT-BR: Cria um arquivo controlador dentro da pasta "app/Controllers/TestController.php"

>> **```php adm controller Test```**

<hr>

> ##### EN-US: Creates a file inside the folder "app/Models/Test.php" and another one in "app/Schemas/tests_capsule.php"

> ##### PT-BR: Cria um arquivo dentro da pasta "app/Models/Test.php" e outro em "app/Schemas/tests_capsule.php"

>> **```php adm model Test```**

<hr>

> ##### EN-US: Run the Schema file (Table) "app/Schemas/tests_capsule.php"

> ##### PT-BT: Executa o arquivo de esquema (tabela) "app/Schemas/tests_capsule.php"

>> **```php adm database Test```**

<hr>

> ##### EN-US: Run all schema files (tables) in the "app/Schemas" folder

> ##### PT-BR: Executa todos os arquivos de esquema (tabelas) no diretório "app/Schemas"

>> **```php adm database --all```**

<hr>

> ##### EN-US: Creates a service file inside the folder "app/Services/TestService.php"

> ##### PT-BR: Cria um arquivo de serviço dentro da pasta "app/Services/TestService.php"

>> **```php adm service Test```**

<hr>

> ##### EN-US: Creates a help file inside the folder "app/Helpers/test.php"

> ##### PT-BR: Cria um arquivo de ajuda dentro da pasta "app/Helpers/test.php"

>> **```php adm helper test```**

<hr>

> ##### EN-US: Creates a view file inside the folder "app/Views/Test.php"

> ##### PT-BR: Cria um arquivo de visão dentro da pasta "app/Views/test.php"

>> **```php adm view test```**

<hr>

> ##### EN-US: Updates the core framework

> ##### PT-BR: Atualiza o core framework

>> **```php adm update```**

<hr>

> ##### EN-US: Testing the default routes

> ##### PT-BR: Testa as rotas padrão

>> **```php adm test```**

<hr>

> ##### EN-US: Zipping files and folders from the `vendor` folder

> ##### PT-BR: Compacta os arquivos e pastas da pasta `vendor`

>> **```php adm zip```**

<hr>

> ##### EN-US: Unzipping the zip files from the `vendor` folder

> ##### PT-BR: Descompacta os arquivos zip da pasta `vendor`

>> **```php adm unzip```**

<hr>

> ##### EN-US: Clears the folder located in `storage/cache/`

> ##### PT-BR: Limpa a pasta localizada em `storage/cache/`

>> **```php adm nocache```**

<hr>

> ##### EN-US: Listing existing routes and listing existing routes by http verb

> ##### PT-BR: Lista as rotas existentes e lista filtrando pelo verbo do http

>> **```php adm route```**

>> **```php adm route:get```**

>> **```php adm route:post```**

>> **```php adm route:put```**

>> **```php adm route:patch```**

>> **```php adm route:options```**

>> **```php adm route:delete```**

<hr>

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
