### Defining Routes

> EN-US:

>> Allows the creation of routes manually, which lead to a method in the controller, which returns a display or has its own function scope (callback)

>> Inside the `app` folder there is a file called `web.php`, which must contain the route settings

>> Example: `app/web.php`

<hr>

### Definindo Rotas

> PT-BR:

>> Possibilita a criação de rotas de maneira manual, que direcionam a um método no controlador, visão ou possui um escopo de função (callback) próprio

>> Dentro da pasta `app` existe um arquivo chamado` web.php`, que deve conter as configurações de rota

>> Exemplo: `app/web.php`

```php
<?php

/*
    EN-US:

        The supported HTTP methods are: GET, POST, PUT, PATCH, DELETE, OPTIONS, ANY and MATCH

        To create a route type Route and the HTTP method name in lower case, example:

            [ Attention:
                The verb "ANY" in practice does not exist, when used it means that the route can be called by any other valid HTTP method.
                The verb "MATCH" in practice does not exist, when used it means that the route can be called by the valid HTTP method that was configured in the array ["GET", "POST", "PUT"]
            ]

            Route::get(), Route::post(), Route::put(), Route::patch(), Route::delete(), Route::options(), Route::any(), Route::match(["GET", "POST"])

        Inside the parentheses it is necessary to inform the parameters for the verb HTTP. 

            [ Attention: The first two parameters are mandatory, the others are not. ]

            - The first parameter corresponds to the route path;
            - The second parameter corresponds to the action to be performed by the route;
            - The third parameter corresponds to the route name;
            - The fourth parameter indicates whether the route should check the CSRF token to allow access to perform the Action (second parameter);
            - The fifth parameter indicates whether the route must verify the JWT token to allow access to perform the Action (second parameter);
            - The fifth parameter indicates whether the route should be cached, the time must be reported in seconds. By default, it is -1, which means no cache.

        Illustration:

            Route::any('PATH = /', 'ACTION = Callback, Controller@Method or Name of view', 'NAME = Name of route', 'CSRF = True or False', 'JWT = True or False', 'CACHE = Time in seconds')


    PT-BR:

        Os métodos HTTP suportados são: GET, POST, PUT, PATCH, DELETE, OPTIONS, ANY e MATCH

        Para criar uma rota, digite Rota e o nome do método HTTP em letras minúsculas, exemplo:

            [ Atenção:
                O verbo "ANY" na prática não existe, quando usado significa que a rota pode ser chamada por qualquer outro método HTTP válido.
                O verbo "MATCH" na prática não existe, quando utilizado significa que a rota pode ser chamada pelo método HTTP válido que foi configurado no array ["GET", "POST", "PUT"]
            ]

            Route::get(), Route::post(), Route::put(), Route::patch(), Route::delete(), Route::options(), Route::any(), Route::match(["GET", "POST"])

        Dentro dos parênteses é necessário informar os parâmetros para o verbo HTTP.
        
            [ Atenção: Os dois primeiros parâmetros são obrigatórios, os demais não. ]

            - O primeiro parâmetro corresponde ao caminho da rota;
            - O segundo parâmetro corresponde à ação a ser realizada pela rota;
            - O terceiro parâmetro corresponde ao nome da rota;
            - O quarto parâmetro indica se a rota deve verificar o token CSRF para permitir o acesso para realizar a Ação (segundo parâmetro);
            - O quinto parâmetro indica se a rota deve verificar o token JWT para permitir o acesso para realizar a Ação (segundo parâmetro);
            - O quinto parâmetro indica se a rota deve ser armazenada em cache, o tempo deve ser informado em segundos. Por padrão, é -1, o que significa nenhum cache.

        Ilustração:

            Route::any('PATH = /', 'ACTION = Função, Controlador@Método ou Nome da visão', 'NAME = Nome da rota', 'CSRF = Verdadeiro ou Falso', 'JWT = Verdadeiro ou Falso', 'CACHE = Tempo em segundos')


*/

use Lib\Route;

Route::get("/template", function () {
    /*
        EN-US: Defines that this route must serve a maximum of three requests per minute
        -
        PT-BR: Define que esta rota deve atender no máximo três solicitações por minuto
    */
    max_requests_per_minute(3, "template");
    /*
        EN-US: Perform data processing on a model and return a string
        -
        PT-BR: Executa o processamento de dados em um modelo (template) e retorna o resultado em um sequência (string)
    */
    $template = view("string_template", [
        "size" => 9,
        "name" => "Matheus Johann Araujo",
    ]);
    return $template;
});

Route::get("/json", function () {
    /*
        EN-US: Returns a JSON
        -
        PT-BR: Retorna um JSON
    */
    return [
        "user" => [
            "name" => "Matheus Johann Araujo",
            "age" => 22
        ]
    ];
});

Route::any("/auth", function () {
    /*
        EN-US: Deliver the JWT code (JSON Web Token)
        -
        PT-BR: Entrega um código JWT (JSON Web Token)
    */
    return input_auth();
});

Route::any("/jwt", function () {
    /*
        EN-US: Delivers an instance of the JWT class with authorization (JSON Web Token code)
        -
        PT-BR: Entrega uma instância da classe JWT com autorização (código JSON Web Token)
    */
    return input_jwt();
});

Route::get("/text", function () {
    /*
        EN-US: Provides the contents of a txt file
        -
        PT-BR: Fornece o conteúdo de um arquivo `text.txt`
    */
    output()
        ->fopen(folder_storage("text.txt"))
        ->name("text.txt")
        ->bitrate(256)
        ->go();
});

Route::get("/video/stream", function () {
    /*
        EN-US: Performs the processing of several mp4 files and delivers it as a stream
        -
        PT-BR: Executa o processamento de vários arquivos mp4 e o entrega como um fluxo (stream)
    */
    output()
        ->fopen(folder_storage("split_video.mp4/"))
        ->name("video.mp4")
        ->go(true);
});

Route::get("/video", function () {
    /*
        EN-US: Performs the processing of several mp4 files and delivers it as a single mp4 file
        -
        PT-BR: Executa o processamento de vários arquivos mp4 e o entrega como um único arquivo mp4
    */
    output()
        ->fopen(folder_storage("split_video.mp4/"))
        ->name("video.mp4")
        //->download(2)
        ->go();
});

Route::get("/math/add/{nums:array}", function (array $nums) {
    /*
        EN-US: Including the "math" helper    
        -
        PT-BR: Incluindo o ajudante de "matemática"        
    */    
    helper("math");
    /*
        EN-US: Returns the result of the sum of the reported numbers
        -
        PT-BR: Retorna o resultado da soma dos números informados
    */
    return [
        "nums" => implode(" + ", $nums),
        "sum" => \math\add(...$nums)
    ];
});

/*
    EN-US: Returns an html page with the data processed in the view template
    -
    PT-BR: Retorna uma página html com os dados processados ​​no modelo de exibição
*/
Route::get("/", "home")::name("home");

/*
    EN-US: Starts route interpretation process
    -
    PT-BR: Inicia o processo de interpretação das rotas
*/
Route::on();

```

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
