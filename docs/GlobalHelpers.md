### Global Helpers | Ajudantes Globais

> *EN-US: The file in `lib/helpers.php` contains the implementation of global helpers (functions), which can be used anywhere in the Continuum framework*

> *PT-BR: O arquivo em `lib/helpers.php` contém a implementação de ajudantes (funções) globais, que podem ser utilizadas em qualquer lugar do framework Continuum*

<hr>

```text
EN-US: Returns an instance of the class `Input` that is stored in the static
variable `$in` of the Route class, and that contains the entry of all data in the system

PT-BR: Retorna uma instância da classe `Input` que é armazenada na variável
estática `$in` da classe `Route` e que contém a entrada de todos os dados no sistema
```
> ### `input()`
<hr>

```text
EN-US: Returns an instance of the class `Output` that is stored in the
Route class static variable `$out`, and that contains the system data output

PT-BR: Retorna uma instância da classe `Output` que é armazenada na variável
estática da classe Route `$out` e que contém a saída de dados do sistema
```
> ### `output()`
<hr>

```text
EN-US: Returns the key and value of the variables of a route

PT-BR: Retorna a chave e o valor das variáveis ​​de uma rota
```
> ### `input_arg($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the `$_ENV` keys and values, including the configuration
that exists within the `.env` file

PT-BR: Retorna as chaves e valores `$_ENV`, incluindo a configuração
existente no arquivo `.env`
```
> ### `input_env($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of `$_REQUEST`

PT-BR: Retorna as chaves e os valores de `$_REQUEST`
```
> ### `input_req($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of `$_GET`

PT-BR: Retorna as chaves e os valores de `$_GET`
```
> ### `input_get($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of `$_POST`

PT-BR: Retorna as chaves e os valores de `$_POST`
```
> ### `input_post($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of `$_FILES`

PT-BR: Retorna as chaves e os valores de `$_FILES`
```
> ### `input_file($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of `$_SERVER`

PT-BR: Retorna as chaves e os valores de `$_SERVER`
```
> ### `input_server($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the keys and values ​​of the `JSON` sent to the server

PT-BR: Retorna as chaves e os valores do `JSON` enviado ao servidor
```
> ### `input_json($key = null, $value_default = null)`
<hr>

```text
EN-US: Returns the `Authorization (JWT code)` that was sent to the server

PT-BR: Retorna a `Autorização (código JWT)` que foi enviada ao servidor
```
> ### `input_auth()`
<hr>

```text
EN-US: Returns an instance of the class `JsonWT` already with the Authorization
(JWT code) that was sent to the server

PT-BR: Retorna uma instância da classe `JsonWT` já com a Autorização
(código JWT) que foi enviada ao servidor
```
> ### `input_jwt()`
<hr>

```text
EN-US: Returns an instance of the `Session` class or the value of a key stored in `$_SESSION`

PT-BR: Retorna uma instância da classe `Session` ou o valor de uma chave armazenada em `$_SESSION`
```
> ### `session($key = null)`
<hr>

```text
EN-US: Returns the `CSRF` code that was generated on the server

PT-BR: Retorna o código `CSRF` que foi gerado no servidor
```
> ### `csrf()`
<hr>

```text
EN-US: Sets or returns messages that are stored in `$_SESSION["__flash__"]`

PT-BR: Define ou retorna mensagens que estam armazenadas em `$_SESSION["__flash__"]`
```
> ### `message(string $key = null, $value = "")`
<hr>

```text
EN-US: Returns the value of a parameter that has been forwarded to the server

PT-BR: Retorna o valor de um parâmetro que foi encaminhado para o servidor
```
> ### `old(string $key, $value_default = null)`
<hr>

```text
EN-US: Returns the link of a route

PT-BR: Retorna o link de uma rota
```
> ### `action(string $path, ...$params)`
<hr>

```text
EN-US: Returns an instance of the `Redirect` class. To redirect
for an address (URL) use the `to(string $path)` method, to redirect
for a route use the `action(string $path, ...$params)` method, and for
return to the previous page use the `back()` method, if you want
that the values ​​of the form fields return, use the method
`withInput()`, `withInputGet()` or `withInputPost()`

PT-BR: Retorna uma instância da classe `Redirect`. Para redirecionar
para um endereço (URL) use o método `to(string $path)`, para redirecionar
para uma rota use o método de `action(string $path, ...$params)`, e para
retornar para à página anterior utilize o método `back()`, se você quiser
que os valores dos campos do formulário retornem, use o método
`withInput()`, `withInputGet()` ou `withInputPost()`
```
> ### `redirect()`
> ### `redirect()->to('https://google.com')`
> ### `redirect()->action('home.index')`
> ### `redirect()->withInput()->back()`
<hr>

```text
EN-US: Returns the result of processing a view

PT-BR: Retorna o resultado do processamento de uma visão (View)
```
> ### `view(string $file, $args = [], int $cache = -1)`
<hr>

```text
EN-US: Returns a hash generated through Argon2, Bcrypt or Default

PT-BR: Retorna um hash gerado através do Argon2, Bcrypt ou Default
```
> ### `hash_generate(string $text, string $alg = "default|argon|bcrypt", array $options = [])`
<hr>

```text
EN-US: Returns the check between text and hash, the result can be true or false

PT-BR: Retorna a verificação entre texto e hash, o resultado pode ser verdadeiro ou falso
```
> ### `hash_verify(string $text, string $hash)`
<hr>

```text
EN-US: Returns a hidden `input` tag that contains the type of method that must be accepted on the server

PT-BR: Retorna uma tag `input` oculta que contém o tipo de método que deve ser aceito no servidor
```
> ### `tag_method(string $method)`
<hr>

```text
EN-US: Returns a hidden `input` tag containing the `CSRF` code that is expected on the server

PT-BR: Retorna uma tag `input` oculta que contém o código `CSRF` que é esperado no servidor
```
> ### `tag_csrf()`
<hr>

```text
EN-US: Returns a tag that delivers the `CSS` styles

PT-BR: Retorna uma tag que fornece os estilos `CSS`
```
> ### `tag_css(string $file, bool $insert_content = false)`
<hr>

```text
EN-US: Returns a script tag that contains JavaScript code

PT-BR: Retorna uma tag script que contém os códigos JavaScript
```
> ### `tag_js(string $file, bool $insert_content = false)`
<hr>

```text
EN-US: Returns tags that include a web page's favicon

PT-BR: Retorna tags que incluem o favicon de uma página da web
```
> ### `tag_favicon(string $file, string $type = "x-icon")`
<hr>

```text
EN-US: Returns an `img` tag that contains the image file address

PT-BR: Retorna uma tag `img` que contém o endereço arquivo de imagem
```
> ### `tag_img(string $file, array $attr = [])`
<hr>

```text
EN-US: Returns a `p` tag containing a message that has been saved to `$_SESSION["__flash__"]`

PT-BR: Retorna uma tag `p` contendo uma mensagem que foi salva em `$_SESSION["__flash__"]`
```
> ### `tag_message(string $key, array $attr = [], string $tag = "p")`
<hr>

```text
EN-US: Returns an `a` tag that contains a route link

PT-BR: Retorna uma tag `a` que contém um link de rota
```
> ### `tag_a(string $name, string $path, array $attr = [], ...$params)`
<hr>

```text
EN-US: Returns the base path of the site

PT-BR: Retorna o caminho base do site
```
> ### `site_url(string $path = "")`
<hr>

```text
EN-US: Performs the inclusion of a file that is inside the Helpers folder

PT-BR: Realiza a inclusão de um arquivo que está dentro da pasta Helpers
```
> ### `helper(string $file)`
<hr>

```text
EN-US: Returns the current number of requests that called this function. If the number of requisitions counter has a value greater than that informed in the parameter `$num_requests` the function launches a message saying what the counter reached the limit value, and then ends the execution of the PHP script.

PT-BR: Retorna o número atual de requisições que chamaram esta função. Se o contador de número de requisições tiver um valor maior que o informado no parâmetro `$num_requests` a função lança uma mensagem dizendo o que o contador atingiu o valor limite e em seguida, encerra a execução do script PHP.
```
> ### `max_requests_per_minute(int $num_requests, string $name_request)`
<hr>

```text
EN-US: Returns the base path of the `public` folder

PT-BR: Retorna o caminho base da pasta `public`
```
> ### `folder_public(string $path = "")`
<hr>

```text
EN-US: Returns the base path of the `storage` folder

PT-BR: Retorna o caminho base da pasta `storage`
```
> ### `folder_storage(string $path = "")`
<hr>

```text
EN-US: Returns the output of a pre-formatted `var_export`

PT-BR: Retorna a saída de um `var_export` pré-formatado
```
> ### `var_export_format(&$data)`
<hr>

```text
EN-US: Prints on the screen the values ​​that were passed in the parameters

PT-BR: Imprime na tela os valores que foram passados ​​nos parâmetros
```
> ### `dumpl(...$params)`
<hr>

```text
EN-US: Print the values ​​that were passed in the parameters
on the screen and end the execution of the php code

PT-BR: Imprime os valores que foram passados ​​nos parâmetros
na tela e finaliza a execução do código php
```
> ### `dumpd(...$params)`
<hr>

```text
EN-US: Returns the conversion of an object to an array

PT-BR: Retorna a conversão de um objeto em uma matriz
```
> ### `object_to_array($object)`
<hr>

```text
EN-US: Returns the conversion of an array of objects to an array

PT-BR: Retorna a conversão de uma matriz de objetos em uma matriz
```
> ### `parse_array_object_to_array($array)`
<hr>

```text
EN-US: Returns the result of an HTTP request using the POST method

PT-BR: Retorna o resultado de uma solicitação HTTP usando o método POST
```
> ### `curl_http_post(string $action, array $data, bool $content_type_is_json = false)`
<hr>

```text
EN-US: Returns a text from `CamelCase` for a lowercase whole separated by `Underline`

PT-BR: Retorna um texto de `CamelCase` para um todo em minúsculas separado por `Underline`
```
> ### `decamelize(string $string)`
<hr>

```text
EN-US: Returns the conversion of the string to the type of the given value

PT-BR: Retorna a conversão da string para o tipo do valor fornecido
```
> ### `string_to_type($val)`
<hr>

```text
EN-US: Returns true or false according to the type and value

PT-BR: Retorna verdadeiro ou falso de acordo com o tipo e valor
```
> ### `is_type(string $type, $val)`
<hr>

```text
EN-US: Returns the MimeType of a file based on its extension

PT-BR: Retorna o MimeType de um arquivo com base em sua extensão
```
> ### `get_mime_type(string $ext)`
<hr>

#### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
