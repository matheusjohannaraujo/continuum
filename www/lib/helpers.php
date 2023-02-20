<?php

use Lib\ENV;
use Lib\URI;
use Lib\View;
use Lib\CSRF;
use Lib\Route;
use Lib\Session;
use Lib\Redirect;
use Lib\DataManager;

function log_exception($e) {
    // Obtem a mensagem da exceção e outras informações relevantes
    $message = $e->getMessage();
    $file = $e->getFile();
    $line = $e->getLine();
    $trace = $e->getTraceAsString();
    $dateTime = date('Y-m-d H:i:s');
    // Monta a mensagem de log
    $logMessage = "[$dateTime] Error: $message in $file line $line\n";
    $logMessage .= "Trace: $trace\n";
    return $logMessage;
}

function create_log($data, string $name = null)
{
    $logMessage = log_exception($data);
    if (is_object($data)) {
        $data = object_to_array($data);
    }
    if (is_array($data) || !is_string($data)) {
        $data = json_encode($data);
    }
    $logMessage .= "StackTraceComplete: " . $data . "\r\n";
    if ($name !== null) {
        $logMessage .= "Marker: $name\r\n";
    }
    $file = folder_storage("log.txt");
    return DataManager::fileAppend($file, $logMessage . "\r\n");
}

function predis_conn()
{
    if (isset($GLOBALS["redis_conn"])) {
        return $GLOBALS["redis_conn"];
    }
    return null;
}

function predis_get(string $key)
{
    $client = predis_conn();
    if ($client !== null) {
        return $client->get($key);
    }
    return null;
}

function predis_set(string $key, $value, int $time = 0)
{
    $client = predis_conn();
    if ($client !== null) {
        if ($time > 0) {
            return $client->setex($key, $time, $value);//seg
            //return $client->psetex($key, $time, $value);//ms
        } else {
            return $client->set($key, $time);
        }
    }
    return false;
}

function predis_del(string $key)
{
    $client = predis_conn();
    if ($client !== null) {
        return $client->del($key);
    }
    return null;
}

/**
 * 
 * **Function -> helper**
 *
 * EN-US: Performs the inclusion of a file that is inside the Helpers folder.
 * 
 * PT-BR: Realiza a inclusão de um arquivo que está dentro da pasta Helpers.
 * 
 * @param string $file
 * @return bool
 */
function helper(string $file)
{
    $folder_helper = __DIR__ . "/../app/" . input_env("NAME_FOLDER_HELPERS") . "/";
    $path = realpath(DataManager::path($folder_helper . "${file}.php"));
    if (DataManager::exist($path) == "FILE") {
        require_once $path;
        return true;
    }
    return false;
}

/**
 * 
 * **Function -> max_requests_per_minute**
 *
 * EN-US: Returns the current number of requests that called this function.
 * If the number of requisitions counter has a value greater than that informed in the
 * parameter `$num_requests` the function launches a message saying what the counter
 * reached the limit value, and then ends the execution of the PHP script.
 * 
 * PT-BR: Retorna o número atual de requisições que chamaram esta função.
 * Se o contador de número de requisições tiver um valor maior que o informado no
 * parâmetro `$num_requests` a função lança uma mensagem dizendo o que o contador
 * atingiu o valor limite e em seguida, encerra a execução do script PHP.
 * 
 * @param int $num_requests
 * @param string $name_request
 * @return int
 */
function max_requests_per_minute(int $num_requests, string $name_request)
{
    $name = "max_requests_per_minute:${name_request}";
    $cache = new \Lib\Cache;
    $time = 60;// 1 minute
    if ($cache->exist($name, $time)) {
        $cache->get_paths($name);
        $count = trim($cache->get());
        if ($count !== null && is_numeric($count)) {
            $count = (int) $count;
        } else {
            dumpd("function max_requests_per_minute -> error: variable \$count is not an integer");
        }
        if ($count >= $num_requests) {
            dumpd("function max_requests_per_minute -> warning: limit reached");
        }
        $cache->put($count + 1);
    } else {
        $cache->init($name, $time);
        $cache->put(1);
    }
    return (int) trim($cache->get());
}

/**
 * 
 * **Function -> request**
 *
 * EN-US: Returns an instance of the class `Request` that is stored in the static
 * variable `$in` of the Route class, and that contains the entry of all data in the system.
 * 
 * PT-BR: Retorna uma instância da classe `Request` que é armazenada na variável
 * estática `$in` da classe `Route` e que contém a entrada de todos os dados no sistema.
 * 
 * @return mixed Request
 */
function request($key = null)
{
    if ($key === null) {
        return Route::$in;
    }
    return Route::$in->get($key);
}

/**
 * 
 * **Function -> response**
 *
 * EN-US: Returns an instance of the class `Response` that is stored in the
 * Route class static variable `$out`, and that contains the system data output.
 * 
 * PT-BR: Retorna uma instância da classe `Response` que é armazenada na variável
 * estática da classe Route `$out` e que contém a saída de dados do sistema.
 * 
 * @return object Response
 */
function response() :\Lib\Response
{
    return Route::$out;
}

/**
 * 
 * **Function -> input**
 *
 * EN-US: Returns an instance of the class `Request` that is stored in the static
 * variable `$in` of the Route class, and that contains the entry of all data in the system.
 * 
 * PT-BR: Retorna uma instância da classe `Request` que é armazenada na variável
 * estática `$in` da classe `Route` e que contém a entrada de todos os dados no sistema.
 * 
 * @return mixed Request
 */
function input($key = null)
{
    if ($key === null) {
        return Route::$in;
    }
    return Route::$in->get($key);
}

/**
 * 
 * **Function -> output**
 *
 * EN-US: Returns an instance of the class `Response` that is stored in the
 * Route class static variable `$out`, and that contains the system data output.
 * 
 * PT-BR: Retorna uma instância da classe `Response` que é armazenada na variável
 * estática da classe Route `$out` e que contém a saída de dados do sistema.
 * 
 * @return object Response
 */
function output() :\Lib\Response
{
    return Route::$out;
}

/**
 * 
 * **Function -> input_arg**
 *
 * EN-US: Returns the key and value of the variables of a route.
 * 
 * PT-BR: Retorna a chave e o valor das variáveis ​​de uma rota.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_arg($key = null, $value_default = null)
{
    return Route::$in->paramArg($key, $value_default);
}

/**
 * 
 * **Function -> input_env**
 *
 * EN-US: Returns the `$_ENV` keys and values, including the configuration
 * that exists within the `.env` file
 * 
 * PT-BR: Retorna as chaves e valores `$_ENV`, incluindo a configuração
 * existente no arquivo `.env`
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_env($key = null, $value_default = null)
{
    if (Route::$in === null) {
        $env = new ENV;
        $env->read();
        return $env->get($key, $value_default);
    }
    return Route::$in->paramEnv($key, $value_default);
}

/**
 * 
 * **Function -> input_req**
 *
 * EN-US: Returns the keys and values ​​of `$_REQUEST`.
 * 
 * PT-BR: Retorna as chaves e os valores de `$_REQUEST`.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_req($key = null, $value_default = null)
{
    return Route::$in->paramReq($key, $value_default);
}

/**
 * 
 * **Function -> input_get**
 *
 * EN-US: Returns the keys and values ​​of `$_GET`.
 * 
 * PT-BR: Retorna as chaves e os valores de `$_GET`.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_get($key = null, $value_default = null)
{
    return Route::$in->paramGet($key, $value_default);
}

/**
 * 
 * **Function -> input_post**
 *
 * EN-US: Returns the keys and values ​​of `$_POST`.
 * 
 * PT-BR: Retorna as chaves e os valores de `$_POST`.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_post($key = null, $value_default = null)
{
    return Route::$in->paramPost($key, $value_default);
}

/**
 * 
 * **Function -> input_file**
 *
 * EN-US: Returns the keys and values ​​of `$_FILES`.
 * 
 * PT-BR: Retorna as chaves e os valores de `$_FILES`.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_file($key = null, $value_default = null)
{
    return Route::$in->paramFile($key, $value_default);
}

/**
 * 
 * **Function -> input_server**
 *
 * EN-US: Returns the keys and values ​​of `$_SERVER`.
 * 
 * PT-BR: Retorna as chaves e os valores de `$_SERVER`.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_server($key = null, $value_default = null)
{
    return Route::$in->paramServer($key, $value_default);
}

/**
 * 
 * **Function -> input_json**
 *
 * EN-US: Returns the keys and values ​​of the `JSON` sent to the server.
 * 
 * PT-BR: Retorna as chaves e os valores do `JSON` enviado ao servidor.
 * 
 * @param mixed $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed|array
 */
function input_json($key = null, $value_default = null)
{
    return Route::$in->paramJson($key, $value_default);
}

/**
 * 
 * **Function -> input_auth**
 *
 * EN-US: Returns the `Authorization (JWT code)` that was sent to the .
 * 
 * PT-BR: Retorna a `Autorização (código JWT)` que foi enviada ao servidor.
 * 
 * @return string
 */
function input_auth()
{    
    return Route::$in->paramAuth();
}

/**
 * 
 * **Function -> input_jwt**
 *
 * EN-US: Returns an instance of the class `JWT` already with the Authorization
 * (JWT code) that was sent to the server.
 * 
 * PT-BR: Retorna uma instância da classe `JWT` já com a Autorização
 * (código JWT) que foi enviada ao servidor.
 * 
 * @return object JWT
 */
function input_jwt()
{
    return Route::$in->paramJwt();
}

/**
 * 
 * **Function -> session**
 *
 * EN-US: Returns an instance of the `Session` class or the value of a key stored in `$_SESSION`.
 * 
 * PT-BR: Retorna uma instância da classe `Session` ou o valor de uma chave armazenada em `$_SESSION`.
 * 
 * @param mixed $key [optional]
 * @return object|mixed Session
 */
function session($key = null)
{
    $session = Session::instance();
    if ($key !== null) {
        return $session->get($key);
    }
    return $session;
}

/**
 * 
 * **Function -> csrf**
 *
 * EN-US: Returns the `CSRF` code that was generated on the server.
 * 
 * PT-BR: Retorna o código `CSRF` que foi gerado no servidor.
 * 
 * @return string
 */
function csrf()
{
    return CSRF::get();
}

/**
 * 
 * **Function -> action**
 *
 * EN-US: Returns the link of a route.
 * 
 * PT-BR: Retorna o link de uma rota.
 * 
 * @param string $path
 * @param mixed ...$params [optional]
 * @return string
 */
function action(string $path, ...$params)
{
    return Route::link($path, $params);
}

/**
 * 
 * **Function -> route**
 *
 * EN-US: Returns the link of a route.
 * 
 * PT-BR: Retorna o link de uma rota.
 * 
 * @param string $path
 * @param mixed ...$params [optional]
 * @return string
 */
function route(string $path, ...$params)
{
    return Route::link($path, $params);
}

/**
 * 
 * **Function -> redirect**
 *
 * EN-US: Returns an instance of the `Redirect` class. To redirect
 * for an address (URL) use the `to(string $path)` method, to redirect
 * for a route use the `action(string $path, ...$params)` method, and for
 * return to the previous page use the `back()` method, if you want
 * that the values ​​of the form fields return, use the method
 * `withInput()`, `withInputGet()` or `withInputPost ()`.
 * 
 * PT-BR: Retorna uma instância da classe `Redirect`. Para redirecionar
 * para um endereço (URL) use o método `to(string $path)`, para redirecionar
 * para uma rota use o método de `action(string $path, ...$params)`, e para
 * retornar para à página anterior utilize o método `back()`, se você quiser
 * que os valores dos campos do formulário retornem, use o método
 * `withInput()`, `withInputGet()` ou `withInputPost()`.
 * 
 * Examples: 
 * 
 * `redirect()`
 * 
 * `redirect()->to('https://google.com')`
 * 
 * `redirect()->action('home.index')`
 * 
 * `redirect()->withInput()->back()`
 * </code>
 * 
 * @return object Redirect
 */
function redirect()
{
    return new Redirect;
}

/**
 * 
 * **Function -> old**
 *
 * EN-US: Returns the value of a parameter that has been forwarded to the server.
 * 
 * PT-BR: Retorna o valor de um parâmetro que foi encaminhado para o servidor.
 * 
 * @param string $key
 * @param mixed $value_default [optional]
 * @return mixed
 */
function old(string $key, $value_default = null)
{
    return session()->get_input($key, $value_default);
}

/**
 * 
 * **Function -> message**
 *
 * EN-US: Sets or returns messages that are stored in `$_SESSION["__flash__"]`.
 * 
 * PT-BR: Define ou retorna mensagens que estam armazenadas em `$_SESSION["__flash__"]`.
 * 
 * @param string $key [optional]
 * @param mixed $value_default [optional]
 * @return mixed
 */
function message(string $key = null, $value = null)
{
    $session = session();
    if ($key === null && $value === null) {
        return $session->get_flash();
    } else if ($key !== null && $value === null) {
        return $session->get_flash($key);
    } else if  ($key !== null && $value !== null) {
        return $session->set_flash($key, $value);
    }
    return null;
}

/**
 *
 * **Function -> view**
 *
 * EN-US: Returns the result of processing a view.
 * 
 * PT-BR: Retorna o resultado do processamento de uma visão (View).
 * 
 * @param string $file
 * @param array $args [optional]
 * @param int $cache_seconds [optional]
 * @return string
 */
function view(string $file, $args = [], int $cache_seconds = -1)
{
    return (new View)->template($file, $args, $cache_seconds);
}

/**
 * 
 * **Function -> hash_generate**
 *
 * EN-US: Returns a hash generated through Argon2, Bcrypt or Default.
 * 
 * PT-BR: Retorna um hash gerado através do Argon2, Bcrypt ou Default.
 * 
 * @param string $text
 * @param string $alg [optional]
 * @param array $options [optional]
 * @return string
 */
function hash_generate(string $text, string $alg = "default", array $options = [])
{
    if ($alg == "argon") {
        $alg = (@constant("PASSWORD_ARGON2ID") ?? @constant("PASSWORD_ARGON2I")) ?? false;
        if (!$alg) {
            throw new Exception("Argon not is suported");
        }
        if (count($options) === 0) {
            $options = [
                'memory_cost' => 2048,
                'time_cost'   => 4,
                'threads'     => 3,
            ];
        }
    } else if ($alg == "bcrypt") {
        $alg = @constant("PASSWORD_BCRYPT") ?? false;
        if (!$alg) {
            throw new Exception("Bcrypt not is suported");
        }
        if (count($options) === 0) {
            $options = ['cost' => 12];
        }
    } else if ($alg == "default") {
        $alg = PASSWORD_DEFAULT;
        $options = [];
    } else {
        throw new Exception("Write in alg (argon, bcrypt or default)");
    }
    return password_hash($text, $alg, $options);
}

/**
 * 
 * **Function -> hash_verify**
 *
 * EN-US: Returns the check between text and hash, the result can be true or false.
 * 
 * PT-BR: Retorna a verificação entre texto e hash, o resultado pode ser verdadeiro ou falso.
 * 
 * @param string $text
 * @param string $hash
 * @return bool
 */
function hash_verify(string $text, string $hash)
{
    return password_verify($text, $hash);
}

// START TAGS ------------------------------------------------------------------------------

/**
 * 
 * **Function -> tag_method**
 *
 * EN-US: Returns a hidden `input` tag that contains the type of method that must be accepted on the server.
 * 
 * PT-BR: Retorna uma tag `input` oculta que contém o tipo de método que deve ser aceito no servidor.
 * 
 * @param string $method [GET, POST, PUT, PATCH, DELETE, OPTIONS]
 * @return string
 */
function tag_method(string $method)
{
    return "<input type=\"hidden\" name=\"_method\" value=\"" . strtoupper($method) . "\">\r\n";
}

/**
 * 
 * **Function -> tag_csrf**
 *
 * EN-US: Returns a hidden `input` tag containing the `CSRF` code that is expected on the server.
 * 
 * PT-BR: Retorna uma tag `input` oculta que contém o código `CSRF` que é esperado no servidor.
 * 
 * @return string
 */
function tag_csrf()
{
    return "<input type=\"hidden\" name=\"_csrf\" value=\"" . CSRF::get() . "\">\r\n";
}

/**
 * 
 * **Function -> tag_css**
 *
 * EN-US: Returns a tag that delivers the `CSS` styles.
 * 
 * PT-BR: Retorna uma tag que fornece os estilos `CSS`.
 * 
 * @param string $file
 * @param bool $insert_content [optional]
 * @return string
 */
function tag_css(string $file, bool $insert_content = false)
{
    if ($insert_content) {
        return "<style type=\"text/css\">\r\n" . file_get_contents(URI::css($file)) . "\r\n</style>\r\n";
    }
    return "<link rel=\"stylesheet\" href=\"" . URI::css($file) . "\">\r\n";
}

/**
 * 
 * **Function -> tag_js**
 *
 * EN-US: Returns a script tag that contains JavaScript code.
 * 
 * PT-BR: Retorna uma tag script que contém os códigos JavaScript.
 * 
 * @param string $file
 * @param bool $insert_content [optional]
 * @return string
 */
function tag_js(string $file, bool $insert_content = false)
{
    if ($insert_content) {
        return "<script type=\"text/javascript\">\r\n" . file_get_contents(URI::js($file)) . "\r\n</script>\r\n";
    }
    return "<script type=\"text/javascript\" src=\"" . URI::js($file) . "\"></script>\r\n";
}

/**
 * 
 * **Function -> tag_favicon**
 *
 * EN-US: Returns tags that include a web page's favicon.
 * 
 * PT-BR: Retorna tags que incluem o favicon de uma página da web.
 * 
 * @param string $file
 * @param string $type [optional]
 * @return string
 */
function tag_favicon(string $file, string $type = "x-icon")
{
    $img = URI::img($file);
    return "<link rel=\"icon\" href=\"$img\" type=\"image/$type\"/>
	<link rel=\"shortcut icon\" href=\"$img\" type=\"image/$type\"/>
    <link rel=\"apple-touch-icon\" href=\"$img\" type=\"image/$type\"/>\r\n";
}

/**
 * 
 * **Function -> tag_img**
 *
 * EN-US: Returns an `img` tag that contains the image file address.
 * 
 * PT-BR: Retorna uma tag `img` que contém o endereço arquivo de imagem.
 * 
 * @param string $file
 * @param array $attr [optional]
 * @return string
 */
function tag_img(string $file, array $attr = [])
{
    $attrs = "";
    foreach ($attr as $key => $value) {
        $attrs .= "$key=\"$value\" ";
    }
    return "<img ${attrs}src=\"" . URI::img($file) . "\">\r\n";
}

// Retorna uma tag `p` que contém uma mensagem que foi salva em `$_SESSION["__flash__"]`

/**
 * 
 * **Function -> tag_message**
 *
 * EN-US: Returns a `p` tag containing a message that has been saved to `$_SESSION["__flash__"]`.
 * 
 * PT-BR: Retorna uma tag `p` contendo uma mensagem que foi salva em `$_SESSION["__flash__"]`.
 * 
 * @param string $key_info
 * @param array $attr [optional]
 * @param string $tag [optional]
 * @return string
 */
function tag_message(string $key_info, array $attr = [], string $tag = "p")
{
    $attrs = "";
    foreach ($attr as $key => $value) {
        $attrs .= "$key=\"$value\" ";
    }
    $message = message($key_info);
    if ($message === null || empty($message)) {
        return "";
    }
    return "<$tag ${attrs}>$message</$tag>\r\n";
}

/**
 * 
 * **Function -> tag_a**
 *
 * EN-US: Returns an `a` tag that contains a route link.
 * 
 * PT-BR: Retorna uma tag `a` que contém um link de rota.
 * 
 * @param string $name
 * @param string $path
 * @param array $attr [optional]
 * @param mixed ...$params [optional]
 * @return string
 */
function tag_a(string $name, string $path, array $attr = [], ...$params)
{
    $link = Route::link($path, $params);
    $attrs = "";
    foreach ($attr as $key => $value) {
        $attrs .= "$key=\"$value\" ";
    }
    return "<a href=\"$link\" ${attrs}>$name</a>\r\n";
}

// STOP TAGS -------------------------------------------------------------------------------

/**
 * 
 * **Function -> site_url**
 *
 * EN-US: Returns the base path of the site.
 * 
 * PT-BR: Retorna o caminho base do site.
 * 
 * @param string $path [optional]
 * @return string
 */
function site_url(string $path = "")
{
    return URI::site($path);
}

/**
 * 
 * **Function -> folder_public**
 *
 * EN-US: Returns the base path of the `public` folder.
 * 
 * PT-BR: Retorna o caminho base da pasta `public`.
 * 
 * @param string $path [optional]
 * @return string
 */
function folder_public(string $path = "")
{
    return URI::public($path);
}

/**
 * 
 * **Function -> folder_storage**
 *
 * EN-US: Returns the base path of the `storage` folder.
 * 
 * PT-BR: Retorna o caminho base da pasta `storage`.
 * 
 * @param string $path [optional]
 * @return string
 */
function folder_storage(string $path = "")
{
    $path = DataManager::path(__DIR__ . "/../storage/$path");
    if (realpath($path) !== false) {
        $path = realpath($path);
    }
    if (DataManager::exist($path) == "FOLDER") {
        $path .=  "/";
    }
    return DataManager::path($path);
}

/**
 * 
 * **Function -> object_to_array**
 *
 * EN-US: Returns the conversion of an object to an array.
 * 
 * PT-BR: Retorna a conversão de um objeto em uma matriz.
 * 
 * @param object $object
 * @return array
 */
function object_to_array($object)
{
    $output = [];
    foreach ((array) $object as $key => $value) {
        $output[preg_replace('/\000(.*)\000/', '', $key)] = $value;
    }
    return $output;
}

/**
 * 
 * **Function -> parse_array_object_to_array**
 *
 * EN-US: Returns the conversion of an array of objects to an array.
 * 
 * PT-BR: Retorna a conversão de uma matriz de objetos em uma matriz.
 * 
 * @param array $array
 * @return array
 */
function parse_array_object_to_array($array)
{
    foreach ($array as $key => $value) {
        if (is_object($value)) {
            $value = object_to_array($value);
            $array[$key] = $value;
        }
        if (is_array($value)) {
            $value = parse_array_object_to_array($value);
            $array[$key] = $value;
        }
    }
    return $array;
}

/**
 *
 * **Function -> decamelize**
 *
 * EN-US: Returns a text from `CamelCase` for a lowercase whole separated by `Underline`.
 *
 * PT-BR: Retorna um texto de `CamelCase` para um todo em minúsculas separado por `Underline`.
 *
 * @param string $text
 * @return string
 */
function decamelize(string $text)
{
    $text = preg_replace("/(?<=\\w)(?=[A-Z])/","_$1", $text);
    return strtolower($text);
}

/**
 *
 * **Function -> base64_url_encode**
 *
 * EN-US: Returns `Base64Url` encoded text that can be passed in the `URL`.
 *
 * PT-BR: Retorna texto codificado em `Base64Url` que pode ser passado no `URL`.
 *
 * @param string $data
 * @return string
 */
function base64_url_encode(string $data): string
{
    return str_replace("=", "", strtr(base64_encode($data), "+/", "-_"));
}

/**
 *
 * **Function -> base64_url_decode**
 *
 * EN-US: Returns the result of removing the `Base64Url` encoding from the text.
 *
 * PT-BR: Retorna o resultado da remoção da codificação `Base64Url` do texto.
 *
 * @param string $data
 * @return string
 */
function base64_url_decode(string $data): string
{
    $remainder = strlen($data) % 4;
    if ($remainder !== 0) {
        $data .= str_repeat("=", 4 - $remainder);
    }
    $decodedContent = base64_decode(strtr($data, "-_", "+/"), true);
    if (!is_string($decodedContent)) {
        throw new \Exception("Error while decoding from Base64: invalid characters used");
    }
    return $decodedContent;
}

/**
 *
 * **Function -> string_to_type**
 *
 * EN-US: Returns the conversion of the string to the type of the given value.
 *
 * PT-BR: Retorna a conversão da string para o tipo do valor fornecido.
 *
 * @param mixed $val
 * @return mixed
 */
function string_to_type($val)
{
    if (is_string($val)) {
        switch (strtolower($val)) {
            case "true":
                $val = true;
                break;
            case "false":
                $val = false;
                break;
            case "null":
                $val = null;
                break;
        }
    }
    if (is_numeric($val)) {
        $str = (string) $val;
        $int = (int) $val;
        $float = (float) $val;
        if ($str !== ((string)$int) && $str !== ((string)$float)) {
            $val = $str;
        } else {
            $val = ($int == $float) ? $int : $float;
        }
    }
    return $val;
}

/**
 *
 * **Function -> type_to_string**
 *
 * EN-US: Returns the conversion of the given value to string.
 *
 * PT-BR: Retorna a conversão do valor fornecido para string.
 *
 * @param mixed $val
 * @return mixed
 */
function type_to_string($val) :string
{
    if (is_bool($val)) {
        return $val ? "true" : "false";
    }
    if ($val === null) {
        //return "null";
        return "";
    }
    return $val;
}

/**
 * 
 * **Function -> is_type**
 *
 * EN-US: Returns true or false according to the type and value.
 * 
 * PT-BR: Retorna verdadeiro ou falso de acordo com o tipo e valor.
 * 
 * @param string $type
 * @param mixed $val
 * @return bool
 */
function is_type(string $type, $val)
{
    $result = false;
    if ($type == "string" && is_string($val)) {
        $result = true;
    }
    if (($type == "int" || $type == "float" || $type == "number") && is_numeric($val)) {
        $val = string_to_type($val);
    }
    if ($type == "int" && is_int($val)) {
        $result = true;
    }
    if ($type == "float" && is_numeric($val) && is_float((float) $val)) {
        $result = true;
    }
    if ($type == "number" && is_numeric($val)) {
        $result = true;
    }
    if ($type == "null" && is_null($val)) {
        $result = true;
    }
    if ($type == "bool" && is_string($val)) {
        $val = string_to_type($val);
        if ($val === 0) {
            $val = false;
        } else if ($val === 1) {
            $val = true;
        }
    }
    if ($type == "bool" && is_bool($val)) {
        $result = true;
    }
    if ($type == "object" && is_object($val)) {
        $result = true;
    }
    if ($type == "array" && is_array($val)) {
        $result = true;
    }
    if ($type == "callback" && is_callable($val)) {
        $result = true;
    }
    return $result;    
}

/**
 * 
 * **Function -> get_mime_type**
 *
 * EN-US: Returns the MimeType of a file based on its extension.
 * 
 * PT-BR: Retorna o MimeType de um arquivo com base em sua extensão.
 * 
 * @param string $ext
 * @return string
 */
function get_mime_type(string $ext)
{
    $ext = strtolower($ext);
    $mime = [
        'txt' => 'text/*; charset=utf-8',
        'htm' => 'text/html; charset=utf-8',
        'html' => 'text/html; charset=utf-8',
        'xhtml' => 'application/xhtml+xml; charset=utf-8',
        'php' => 'text/plain; charset=utf-8',
        'ino' => 'text/plain; charset=utf-8',
        'java' => 'text/plain; charset=utf-8',
        'c' => 'text/plain; charset=utf-8',
        'cpp' => 'text/plain; charset=utf-8',
        'kt' => 'text/plain; charset=utf-8',
        'sql' => 'application/sql',
        'php' => 'text/plain; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'xml' => 'application/xml; charset=utf-8',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'webp' => 'image/webp',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'wav' => 'audio/wav',
        'oga' => 'audio/ogg',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'ogv' => 'video/ogg',
        'mov' => 'video/quicktime',
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mpeg' => 'video/mpeg',
        'webm' => 'video/webm',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

        // font
        'otf' => 'font/otf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    foreach ($mime as $key => $value) {
        if ($key == $ext) {
            return $value;
        }
    }
    return 'application/octet-stream';
}

/**
 * 
 * **Function -> I18N**
 *
 * EN-US: Returns a value based on the language, and key entered. If the key does not exist, a default value is returned by the function.
 * 
 * PT-BR: Retorna um valor com base no idioma, e chave informada. Se não existir a chave, um valor padrão é devolvido pela função.
 * 
 * @param string $lang
 * @param string $key
 * @param mixed $default_value [optional]
 * @return mixed
 */
function I18N(string $lang, string $key, $default_value = null) {
    if (!isset(__I18N__[$key]) || !isset(__I18N__[$key][$lang])) {
        return $default_value;
    } 
    return __I18N__[$key][$lang];
}

/**
 * 
 * **Function -> I18N_lang_init**
 *
 * EN-US: Defines a default language in the `$_SESSION["lang"]` session to be used by the I18N_session function.
 * 
 * PT-BR: Define um idioma padrão na sessão `$_SESSION["lang"]` a ser usado pela função I18N_session.
 * 
 * @return bool
 */
function I18N_lang_init()
{
    if (session("lang") === null) {
        session()->set("lang", input_env("MAIN_LANGUAGE", "pt-br"));
        return true;
    }
    return false;
}

I18N_lang_init();

/**
 * 
 * **Function -> I18N_session**
 *
 * EN-US: Fetch the key and return a value based on the language that is defined in the `$_SESSION["lang"]` session.
 * 
 * PT-BR: Busca a chave e retorna um valor com base na linguagem que esta definida na sessão `$_SESSION["lang"]`.
 *
 * @param string $key
 * @param mixed $default_value [optional]
 * @return mixed
 */
function I18N_session(string $key, $default_value = null) {
    I18N_lang_init();
    return I18N(session("lang"), $key, $default_value);
}

/**
 * 
 * **Function -> curl_http_post**
 *
 * EN-US: Returns the result of an HTTP request using the POST method.
 * 
 * PT-BR: Retorna o resultado de uma solicitação HTTP usando o método POST.
 * 
 * @param string $action
 * @param array $data
 * @param bool $content_type_is_json [optional]
 * @return mixed|array
 */
function curl_http_post(string $action, array $data, bool $content_type_is_json = false)
{
    $cURL = curl_init();
    curl_setopt($cURL, CURLOPT_URL, $action);
    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURL, CURLOPT_POST, true);
    if ($content_type_is_json) {
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $data = json_encode($data);
    }
    curl_setopt($cURL, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($cURL);
    curl_close($cURL);
    return json_decode($output) ?? $output;
}

/**
 * 
 * **Function -> verifyClassMethod**
 *
 * EN-US: Returns an array containing the scan result of a class/method that was informed in the string.
 * 
 * PT-BR: Retorna uma matriz contendo o resultado da verificação de uma classe/método informada na string.
 * 
 * @param string $ClassMethod (Namespace\Class@Method, Namespace\Class->Method, Namespace\Class::Method)
 * @return array
*/
function verifyClassMethod(string $ClassMethod) :array
{
    $caracs = ["@", "::", "->"];
    $carac = null;
    for ($i = 0; $i < count($caracs); $i++) { 
        if (strpos($ClassMethod, $caracs[$i]) !== false) {
            $carac = $caracs[$i];
        }
    }
    if ($carac !== null) {
        $ClassMethod = explode($carac, $ClassMethod);
        if (!class_exists($ClassMethod[0]) || !method_exists($ClassMethod[0], $ClassMethod[1])) {
            $ClassMethod = [null, null];
        }
    } else {
        $ClassMethod = [null, null];
    }
    return [
        "class" => $ClassMethod[0],
        "method" => $ClassMethod[1],
        "carac" => $carac
    ];
}

/**
 * 
 * **Function -> callClassMethod**
 *
 * EN-US: Returns the result of the call of a method in a class. The first informed parameter should be in the standard returned by Function `VerifyClassMethod`.
 * 
 * PT-BR: Retorna o resultado da chamada de um método em uma classe. O primeiro parâmetro informado deve estar no padrão retornado pela função `verifyclassmethod`.
 * 
 * @param array $returnVerifyClassMethod
 * @param array $argsMethod [optional, default = null]
 * @param array $argsConstructor [optional, default = null]
 * @return array
*/
function callClassMethod(array $returnVerifyClassMethod, array $argsMethod = [], array $argsConstructor = [])
{
    if (
        $returnVerifyClassMethod["class"] === null ||
        $returnVerifyClassMethod["method"] === null ||
        $returnVerifyClassMethod["carac"] === null
    ) {
        return;
    }
    $class = $returnVerifyClassMethod["class"];
    $method = $returnVerifyClassMethod["method"];
    $carac = $returnVerifyClassMethod["carac"];
    if ($carac == "@" || $carac == "->") {
        return (new $class(...$argsConstructor))->$method(...$argsMethod);
    } else if ($returnVerifyClassMethod["carac"] == "::") {
        return ($class)::$method(...$argsMethod);
    }
}

function command_exec(string $nameFile, array $params = [])
{
    $folderCommandName = input_env("NAME_FOLDER_COMMANDS");
    $baseDir = realpath(__DIR__ . "/../");
    $file = DataManager::path($baseDir . "/app/${folderCommandName}/${nameFile}.php");
    if (DataManager::exist($file) == 'FILE') {
        (function() use ($file, $params) {
            try {
                require_once $file;
                workWait(function() { usleep(1); });                
            } catch (\Throwable $th) {
                create_log($th);
                dumpl($th);
            }
        })();
    } else {
        echo PHP_EOL, "Command file not found: ", $file, PHP_EOL;
    }
}
