<?php

define("CLI", true);

require_once __DIR__ . "/../vendor/autoload.php";

use Lib\ENV;
use Lib\DataManager;

$BASE_DIR = realpath(__DIR__ . "/../");
define("__BASE_DIR__", $BASE_DIR);

function fun_routes(string $method = "")
{
    $routes = file_get_contents(input_env("APP_URL") . "routes/all/json/$method");
    $routes = (array) json_decode($routes, true);
    foreach ($routes as $key => $route) {
        echo "---------------------------------------------------------------------";
        echo cli_text_color(
            "\r\n METHOD: " . $route["method"] .
            "\r\n PATH: " . $route["path"] .
            "\r\n ACTION: " . $route["action"] .
            "\r\n NAME: " . $route["name"] .
            "\r\n CSRF: " . ($route["csrf"] ? "enabled" : "disabled") .
            "\r\n JWT: " . ($route["jwt"] ? "enabled" : "disabled") .
            "\r\n CACHE: " . ($route["cache"] > 0 ? $route["cache"] . "s" : ($route["cache"] == 0 ? "infinite" : "disabled")) . "\r\n",
        "yellow", "black");
    }
    echo "---------------------------------------------------------------------", PHP_EOL;
}

function fun_test_route(string $name, string $test_md5, string $uri)
{
    $page = @file_get_contents($uri);
    $md5 = md5($page);
    echo " ", ($test_md5 == $md5) ? "OK" : "FAIL", " | $md5 | $name | $uri\r\n";
}

function fun_test_routes()
{
    $baseDomain = site_url();
    echo "\r\n";
    fun_test_route("/.env", "d41d8cd98f00b204e9800998ecf8427e", $baseDomain . ".env");
    echo "\r\n";
    fun_test_route("/storage/text.txt", "d41d8cd98f00b204e9800998ecf8427e", $baseDomain . "storage/text.txt");
    echo "\r\n";
    fun_test_route("/js/index.js", "6c137fc4b54929449be0902a208c84ac", $baseDomain . "js/index.js");
    echo "\r\n";
    fun_test_route("/public/js/index.js", "6c137fc4b54929449be0902a208c84ac", $baseDomain . "public/js/index.js");
    echo "\r\n";
    fun_test_route("/", "9d07452d7546c50c1e7148f9768755e1", $baseDomain);
    echo "\r\n";
    fun_test_route("/template", "3f8becb706c82ee299cdc91619c4700d", $baseDomain . "template");
    echo "\r\n";
    fun_test_route("/json", "48897a19a821580ff2d187c7d3af552f", $baseDomain . "json");
    echo "\r\n";
    fun_test_route("/math/add/3/5", "f07ec6620f6e1893f5babbd51829ba7d", $baseDomain . "math/add/3/5");
    echo "\r\n";
    fun_test_route("/api/v1/text", "75e8c9b8c1a48bdb1fb5f72ca5ffa882", $baseDomain . "api/v1/text");
    echo "\r\n";
    fun_test_route("/api/v1/video", "aa08e10eb9b3c8424429cf15fe8e2fe6", $baseDomain . "api/v1/video");
    echo "\r\n";
    fun_test_route("/api/v1/video/stream", "aa08e10eb9b3c8424429cf15fe8e2fe6", $baseDomain . "api/v1/video/stream");
    echo "\r\n";
    fun_test_route("/contact", "d31ac1c5f7c3abafb1b31640854db673", $baseDomain . "contact");
    echo "\r\n";
}

function fun_create_app_file(string $class, string $content, string $pathFile)
{
    if($class != "" && $content != "" && $pathFile != ""){
        $pathFile = __BASE_DIR__ . "/app/" . $pathFile;
        DataManager::fileWrite($pathFile, $content);
        if(file_exists($pathFile) && is_file($pathFile)){
            echo "File created in \"$pathFile\"\r\n";
            echo "Content:\r\n\r\n$content";
        }
    }
}

function fun_create_controller(string $nameFile, bool $require = true)
{
    $folderServiceName = input_env("NAME_FOLDER_SERVICES");
    $folderControllerName = input_env("NAME_FOLDER_CONTROLLERS");
    $pathinfo = pathinfo($nameFile);
    $dirname = $pathinfo['dirname'];
    $filename = $pathinfo['filename'];
    $namespace = "";
    $pathroute = "";
    if ($dirname != ".") {
        $namespace = "\\" . str_replace("/", "\\", $dirname);
        $pathroute = strtolower("/" . str_replace("\\", "/", $dirname));
    }
    $dircontroller = DataManager::path(__BASE_DIR__ . "/app/${folderControllerName}/${dirname}/");
    if (!DataManager::exist($dircontroller)) {
        DataManager::folderCreate($dircontroller);
    }
    $class = "${filename}Controller";
    $pathFile = "${folderControllerName}/${dirname}/${class}.php";
    $methods = "";
    if ($require) {
        $nameFileLower = strtolower($filename);
        if (DataManager::exist(__BASE_DIR__ . "/app/${folderServiceName}/${filename}Service.php") == "FILE") {
            $require = "\r\nuse App\\${folderServiceName}\\${filename}Service;\r\n";
            $methods = "private \$${nameFileLower}Service;
            
    public function __construct(){
        \$this->${nameFileLower}Service = new ${filename}Service;
    }\r\n
    ";
        } else {
            $require = "";
        }
        $methods .= "/*

        Creating routes from the methods of a controller dynamically
        ------------------------------------------------------------------------------------------------
        This array below configures how the route works
        ------------------------------------------------------------------------------------------------
        array \$CONFIG = [
            'method' => 'POST',
            'csrf' => false,
            'jwt' => false,
            'cache' => -1,
            'name' => 'test.create'
        ]
        ------------------------------------------------------------------------------------------------
        To use the route, it is necessary to inform the name of the Controller, the name of the Method 
        and the value of its parameters, the `array parameter \$CONFIG` being only for configuration
        ------------------------------------------------------------------------------------------------
        Examples of use the routes:

            Controller = ${nameFile}Controller
            Method = action
            Call = ${nameFile}Controller@action(...params)
        ------------------------------------------------------------------------------------------------
            | HTTP Verb | ${nameFile}Controller@method   | PATH ROUTE
        ------------------------------------------------------------------------------------------------
            | GET       | ${nameFile}Controller@index    | ${pathroute}/${nameFileLower}/index
            | GET       | ${nameFile}Controller@new      | ${pathroute}/${nameFileLower}/new
            | POST      | ${nameFile}Controller@create   | ${pathroute}/${nameFileLower}/create
            | GET       | ${nameFile}Controller@show     | ${pathroute}/${nameFileLower}/show/1
            | GET       | ${nameFile}Controller@edit     | ${pathroute}/${nameFileLower}/edit/1
            | PUT       | ${nameFile}Controller@update   | ${pathroute}/${nameFileLower}/update/1
            | DELETE    | ${nameFile}Controller@destroy  | ${pathroute}/${nameFileLower}/destroy/1
        ------------------------------------------------------------------------------------------------
            
    */

    // This variable informs that the public methods of this controller must be automatically mapped in routes
    private \$generateRoutes;   

    // List all ${nameFileLower}
    public function index(array \$CONFIG = [\"method\" => \"GET\"])
    {
        return \"${nameFile}Controller@index()\";
    }

    // Redirect page - Create a single ${nameFileLower}
    public function new(array \$CONFIG = [\"method\" => \"GET\"])
    {
        return \"${nameFile}Controller@new()\";
    }

    // Create a single ${nameFileLower}
    public function create(array \$CONFIG = [\"method\" => \"POST\", \"csrf\" => true])
    {
        return \"${nameFile}Controller@create()\";
    }

    // Get single ${nameFileLower}
    public function show(int \$id, array \$CONFIG = [\"method\" => \"GET\"])
    {
        return \"${nameFile}Controller@show(\$id)\";
    }

    // Redirect page - Update a single ${nameFileLower}
    public function edit(int \$id, array \$CONFIG = [\"method\" => \"GET\"])
    {
        return \"${nameFile}Controller@edit(\$id)\";
    }

    // Update a single ${nameFileLower}
    public function update(int \$id, array \$CONFIG = [\"method\" => \"PUT\", \"csrf\" => true])
    {
        return \"${nameFile}Controller@update(\$id)\";
    }

    // Destroy a single ${nameFileLower}
    public function destroy(int \$id, array \$CONFIG = [\"method\" => \"DELETE\", \"csrf\" => true])
    {
        return \"${nameFile}Controller@destroy(\$id)\";
    }";
    } else {
        $require = "";
    }
    $content = "<?php

namespace App\\${folderControllerName}${namespace};
$require
class ${filename}Controller
{

    $methods

}
";

    // dumpd($class, $content, $pathFile);
    fun_create_app_file($class, $content, $pathFile);
}

function fun_create_service(string $nameFile, bool $require = false)
{
    $folderModelName = input_env("NAME_FOLDER_MODELS");
    $folderServiceName = input_env("NAME_FOLDER_SERVICES");
    $class = "${nameFile}Service";
    $pathFile = "/${folderServiceName}/${class}.php";
    $constructor = "";
    if ($require) {
        $require = "\r\nuse App\\${folderModelName}\\${nameFile};\r\n";
        $instance = strtolower($nameFile);
        $constructor = "\r\n    private \$$instance;\r\n
    public function __construct()
    {
        \$this->$instance = new $nameFile();
        \$this->${instance}->create();
    }\r\n";
    }
    $content = "<?php

namespace App\\${folderServiceName};
$require        
class $class
{
    $constructor
}
";
    fun_create_app_file($class, $content, $pathFile);
}

function fun_create_helper(string $nameFile, $require = false)
{
    $folderHelperName = input_env("NAME_FOLDER_HELPERS");
    $pathinfo = pathinfo($nameFile);
    $dirname = $pathinfo['dirname'];
    $dircontroller = DataManager::path(__BASE_DIR__ . "/app/${folderHelperName}/${dirname}/");
    if (!DataManager::exist($dircontroller)) {
        DataManager::folderCreate($dircontroller);
    }
    $class = "${nameFile}";
    $pathFile = "/${folderHelperName}/${class}.php";
    $content = "<?php

" . ($require ? "namespace $nameFile;\r\n\r\n" : "");
    fun_create_app_file($class, $content, $pathFile);
}

function fun_create_middleware(string $nameFile, $require = false)
{
    $folderMiddlewareName = input_env("NAME_FOLDER_MIDDLEWARES");
    $pathinfo = pathinfo($nameFile);
    $dirname = $pathinfo['dirname'];
    $dircontroller = DataManager::path(__BASE_DIR__ . "/app/${folderMiddlewareName}/${dirname}/");
    if (!DataManager::exist($dircontroller)) {
        DataManager::folderCreate($dircontroller);
    }
    $class = "${nameFile}";
    $pathFile = "/${folderMiddlewareName}/${class}.php";
    $content = "<?php

namespace App\\${folderMiddlewareName};

class ${nameFile} {

    public static function handle(\$route, \Closure \$next) :bool
    {
        dumpl(\"handle\", \$route);
        return \$next(rand(0, 1));
    }

}
";
    fun_create_app_file($class, $content, $pathFile);
}

function fun_create_model(string $nameFile, $require = false)
{
    if (!$require) {
        $columns = "        'name',
        'email'";
        $typesAndColumns = "    \$table->string('name');

    \$table->string('email')->unique();";
    } else {
        $columns = "";
        $typesAndColumns = "";
        $require = explode(",", $require);    
        foreach ($require as $key => $value) {
            $value = explode(":", $value);
            if (count($value) == 2) {
                $type = trim($value[0]);
                $column = trim($value[1]);
                $columns .= "       '$column',\r\n";
                $typesAndColumns .= "    \$table->$type('$column');\r\n\r\n";
            } else if (count($value) == 1) {
                $type = "string";
                $column = trim($value[0]);
                $columns .= "       '$column',\r\n";
                $typesAndColumns .= "    \$table->$type('$column');\r\n\r\n";
            }
        }
        $columns = rtrim($columns);
        $typesAndColumns = rtrim($typesAndColumns);
    }
    // dumpd($require, $columns, $typesAndColumns);
    $folderModelName = input_env("NAME_FOLDER_MODELS");
    $class = "${nameFile}";
    $pathFile = "/${folderModelName}/${class}.php";
    $table = strtolower($class) . "s";
    $content = "<?php

namespace App\\${folderModelName};

use Illuminate\Database\Eloquent\Model as Eloquent;

class ${class} extends Eloquent
{

    protected \$table = '$table';

	protected \$fillable = [
$columns
    ];

}
";
    fun_create_app_file($class, $content, $pathFile);
    echo "\r\n";
    $class = $table;
    $folderSchemaName = input_env("NAME_FOLDER_SCHEMAS");
    $pathFile = "/${folderSchemaName}/${class}_capsule.php";
    $content = "<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('${class}');

Capsule::schema()->create('${class}', function (\$table) {

    \$table->increments('id');

$typesAndColumns

    \$table->timestamps();

});
";
    fun_create_app_file($class, $content, $pathFile);
}

function fun_create_view(string $nameFile)
{
    $folderViewName = input_env("NAME_FOLDER_VIEWS");
    $pathinfo = pathinfo($nameFile);
    $dirname = $pathinfo['dirname'];
    $dircontroller = DataManager::path(__BASE_DIR__ . "/app/${folderViewName}/${dirname}/");
    if (!DataManager::exist($dircontroller)) {
        DataManager::folderCreate($dircontroller);
    }
    $class = "${nameFile}";
    $pathFile = "/${folderViewName}/${class}.php";
    $content = "<!DOCTYPE html>
<html lang=\"pt-BR\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>${nameFile}</title>
</head>
<body>
    <h1>Welcome to page ${nameFile}</h1>
    <?php dumpl(\$_ARGS); ?>
</body>
</html>
";
    fun_create_app_file($class, $content, $pathFile);
}

function fun_init_server(int $port = 80)
{
    //$basename = pathinfo(__BASE_DIR__)["basename"];
    //$URL = "http://127.0.0.1:$port/$basename/";
    $URL = "http://127.0.0.1:$port/";
    echo "\r\n";
    echo "URI address: $URL";
    echo "\r\n";
    echo "Server port: $port";
    echo "\r\n";
    /*var_dump(PHP_OS);
    shell_exec("start $URL");
    shell_exec("xdg-open $URL || sensible-browser $URL || x-www-browser $URL || gnome-open $URL");*/
    //shell_exec("cd .. && php -S 0.0.0.0:$port/$basename/");
    shell_exec("php -S 0.0.0.0:$port");
    die;
}

function fun_folder_denied(string $basedir)
{
    DataManager::fileWrite($basedir . ".htaccess", "
<IfModule authz_core_module>
    Require all denied
</IfModule>
<IfModule !authz_core_module>
    Deny from all
</IfModule>
");
    DataManager::fileWrite($basedir . "index.html", "
<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
</head>
<body>
    <h1>Directory access is forbidden.</h1>
</body>
</html>
");
}

function fun_clean_simple_mvcs()
{
    echo cli_text_color("\r\n Cleaning up . . .");
    $folderViewName = input_env("NAME_FOLDER_VIEWS");
    $folderModelName = input_env("NAME_FOLDER_MODELS");
    $folderHelperName = input_env("NAME_FOLDER_HELPERS");
    $folderSchemaName = input_env("NAME_FOLDER_SCHEMAS");
    $folderCommandName = input_env("NAME_FOLDER_COMMANDS");
    $folderServiceName = input_env("NAME_FOLDER_SERVICES");
    $folderControllerName = input_env("NAME_FOLDER_CONTROLLERS");
    $folderMiddlewareName = input_env("NAME_FOLDER_MIDDLEWARES");
    $basedir = __BASE_DIR__ . "/app/";
    DataManager::delete($basedir);
    DataManager::folderCreate($basedir . "${folderSchemaName}");
    fun_folder_denied($basedir . "${folderSchemaName}/");
    DataManager::folderCreate($basedir . "${folderHelperName}");
    fun_folder_denied($basedir . "${folderHelperName}/");
    DataManager::folderCreate($basedir . "${folderControllerName}");
    fun_folder_denied($basedir . "${folderControllerName}/");
    DataManager::folderCreate($basedir . "${folderMiddlewareName}");
    fun_folder_denied($basedir . "${folderMiddlewareName}/");
    DataManager::folderCreate($basedir . "${folderModelName}");
    fun_folder_denied($basedir . "${folderModelName}/");
    DataManager::folderCreate($basedir . "${folderCommandName}");
    fun_folder_denied($basedir . "${folderCommandName}/");
    DataManager::folderCreate($basedir . "${folderServiceName}");
    fun_folder_denied($basedir . "${folderServiceName}/");
    DataManager::folderCreate($basedir . "${folderViewName}");
    fun_folder_denied($basedir . "${folderViewName}/");
    DataManager::fileWrite($basedir . "common.php", "<?php\r\n
const __I18N__ = [
    \"hello\" => [
        \"en-us\" => \"Hello\",
        \"pt-br\" => \"Olá\"
    ]
];
");
    DataManager::fileWrite($basedir . "${folderViewName}/page_message.php", "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?= \$title; ?></title>
</head>
<body>
    <style>
        body{
            background: #ccc;
            color: #b00;
            text-shadow: 1px 1px 1px #888;
        }
        div{
            margin-top: 350px;
            margin-bottom: 350px;
            text-align: center;
        }
    </style>
    <div>
        <?= \$body; ?>
    </div>            
</body>
</html>
");
    fun_folder_denied($basedir);
    DataManager::fileWrite($basedir . "web.php", "<?php

use Lib\Route;

Route::get(\"/\", function() {
    return \"<h1>Hello World</h1>\";
});

Route::on();
");
    $basedir = __BASE_DIR__ . "/storage/";
    DataManager::delete($basedir);
    DataManager::folderCreate($basedir);
    fun_folder_denied($basedir);
    DataManager::folderCreate($basedir . "cache/");
    fun_folder_denied($basedir . "cache/");

    $basedir = __BASE_DIR__ . "/vendor/";
    DataManager::folderCreate($basedir);
    fun_folder_denied($basedir);

    $basedir = __BASE_DIR__ . "/public/";
    DataManager::delete($basedir);
    DataManager::folderCreate($basedir . "css");
    DataManager::folderCreate($basedir . "js");
    DataManager::folderCreate($basedir . "img");
    DataManager::fileWrite($basedir . ".htaccess", "
# Enable directory browsing 
Options +Indexes
            
# Show the contents of directories
IndexIgnoreReset ON

<Files *.*>
    Order Deny,Allow
    Allow from all
</Files>

<Files *>
    Order Deny,Allow
    Allow from all
</Files>
");
    DataManager::fileWrite($basedir . "robots.txt", "
User-agent: *
Disallow:
");
    echo cli_text_color("\r\n Clean!\r\n");
}

function fun_update_project()
{
    $folderActual = DataManager::path(realpath(__DIR__ . "/../"));
    /*if (DataManager::exist($folderActual . ".git") == "FOLDER") {        
        exit(shell_exec("cd $folderActual && git pull"));
    }*/
    $folderUpdate = DataManager::path($folderActual . "continuum-master/www/");
    echo "\r\n";
    echo cli_text_color(" Dir actual: " . $folderActual);
    echo "\r\n";
    echo cli_text_color(" Dir updated: " . $folderUpdate);
    echo "\r\n";
    echo cli_text_color(" Download . . .", "cyan");
    echo "\r\n";
    $link = "https://github.com/matheusjohannaraujo/continuum/archive/master.zip";
    $zip = file_get_contents($link);
    $zipName = "continuum-master.zip";
    file_put_contents($folderActual . $zipName, $zip);

    DataManager::zipExtract($folderActual . $zipName, $folderActual);
    DataManager::delete($zipName);

    DataManager::delete($folderUpdate . "app/");
    DataManager::delete($folderUpdate . "public/");
    DataManager::delete($folderUpdate . "storage/");
    DataManager::delete($folderUpdate . "database.sqlite");
    DataManager::delete($folderUpdate . "composer.lock");
    DataManager::copy($folderActual . "app/", $folderUpdate . "app/");
    DataManager::copy($folderActual . "public/", $folderUpdate . "public/");
    DataManager::copy($folderActual . "storage/", $folderUpdate . "storage/");
    DataManager::copy($folderActual . ".env", $folderUpdate . ".env_old");
    DataManager::copy($folderActual . ".gitignore", $folderUpdate . ".gitignore_old");
    DataManager::copy($folderActual . "composer.json", $folderUpdate . "composer_old.json");

    $folderUpdateFinal = $folderActual . "../" . pathinfo($folderActual)["basename"] . "_" . date("Y_m_d_H_i_s") . "/";

    DataManager::move($folderUpdate, $folderUpdateFinal);

    DataManager::delete(DataManager::path($folderActual . "continuum-master/"));

    echo cli_text_color(" Dir updated: " . $folderUpdateFinal);
}

function fun_list_commands()
{
    $folderViewName = input_env("NAME_FOLDER_VIEWS");
    $folderModelName = input_env("NAME_FOLDER_MODELS");
    $folderHelperName = input_env("NAME_FOLDER_HELPERS");
    $folderSchemaName = input_env("NAME_FOLDER_SCHEMAS");
    $folderServiceName = input_env("NAME_FOLDER_SERVICES");
    $folderCommandName = input_env("NAME_FOLDER_COMMANDS");
    $folderControllerName = input_env("NAME_FOLDER_CONTROLLERS");
    $folderMiddlewareName = input_env("NAME_FOLDER_MIDDLEWARES");
    $version_actual = input_env("VERSION", "very old");
    $env = new ENV;
    $env->read("https://raw.githubusercontent.com/matheusjohannaraujo/continuum/master/www/.env.example");
    $version_latest = $env->get("VERSION", "not found");
    echo "
 ###################################################################################################
 #
 # " . cli_text_color("Continuum - A simple and complete PHP framework, thought and designed by Matheus Johann Araújo", "blue") . "
 #
 # -------------------------------------------------------------------------------------------------
 #
 # The local version of the Continuum framework is " . cli_text_color("`$version_actual`", "red") . " and the remote version is " . cli_text_color("`$version_latest`") . "
 #
 # Version: " . cli_text_color("`$version_actual`", "red") . " -> " . cli_text_color("`$version_latest`") . "
 #
 # To update the core of the framework, use the command " . cli_text_color("`php adm update`", "yellow") . "
 #
 ###################################################################################################
 " . cli_text_color("
 COMMAND COMPLETE        | DESCRIPTION", "purple") . "
 ---------------------------------------------------------------------------------------------------
 php adm help            | List all commands
 ---------------------------------------------------------------------------------------------------
 php adm clean           | Clears the project, leaving only the default settings
 ---------------------------------------------------------------------------------------------------
 php adm server          | Start a web server on port 80
 ---------------------------------------------------------------------------------------------------
 php adm controller Test | Creates a file inside the folder \"app/${folderControllerName}/TestController.php\"
 ---------------------------------------------------------------------------------------------------
 php adm command test    | Run the command file inside the folder \"app/${folderCommandName}/test.php\"
 ---------------------------------------------------------------------------------------------------
 php adm middleware Test | Creates a file inside the folder \"app/${folderMiddlewareName}/Test.php\"
 ---------------------------------------------------------------------------------------------------
 php adm model Test      | Creates a file inside the folder \"app/${folderModelName}/Test.php\"
                           and another one in \"app/${folderSchemaName}/tests_capsule.php\"
 ---------------------------------------------------------------------------------------------------
 php adm database Test   | Run the Schema file (Table) \"app/${folderSchemaName}/tests_capsule.php\"
 ---------------------------------------------------------------------------------------------------
 php adm database --all  | Run all schema files (tables) in the \"app/${folderSchemaName}\" folder
 ---------------------------------------------------------------------------------------------------
 php adm service Test    | Creates a file inside the folder \"app/${folderServiceName}/TestService.php\"
 ---------------------------------------------------------------------------------------------------
 php adm helper test     | Creates a file inside the folder \"app/${folderHelperName}/test.php\"
 ---------------------------------------------------------------------------------------------------
 php adm view test       | Creates a file inside the folder \"app/${folderViewName}/test.php\"
 ---------------------------------------------------------------------------------------------------
 php adm update          | Updates the core framework
 ---------------------------------------------------------------------------------------------------
 php adm test            | Testing the default routes
 ---------------------------------------------------------------------------------------------------
 php adm zip             | Zipping files and folders from the `vendor` folder
 ---------------------------------------------------------------------------------------------------
 php adm unzip           | Unzipping the zip files from the `vendor` folder
 ---------------------------------------------------------------------------------------------------
 php adm nocache         | Clears the folder located in `storage/cache/`
 ---------------------------------------------------------------------------------------------------
 php adm route           | Listing existing routes and listing existing routes by http verb
 ---------------------------------------------------------------------------------------------------
 php adm route:get       | Lists existing routes by the http GET verb
 ---------------------------------------------------------------------------------------------------
 php adm route:post      | Lists existing routes by the http POST verb
 ---------------------------------------------------------------------------------------------------
 php adm route:put       | Lists existing routes by the http PUT verb
 ---------------------------------------------------------------------------------------------------
 php adm route:patch     | Lists existing routes by the http PATCH verb
 ---------------------------------------------------------------------------------------------------
 php adm route:options   | Lists existing routes by the http OPTIONS verb
 ---------------------------------------------------------------------------------------------------
 php adm route:delete    | Lists existing routes by the http DELETE verb  
", PHP_EOL;
}

function fun_apply_database(string $nameFile)
{
    require_once __DIR__ . "/db_conn_capsule.php";
    db_schemas_apply($nameFile);
}

function fun_no_cache()
{
    echo cli_text_color("\r\n Clearing the cache . . .\r\n\r\n");
    foreach (DataManager::folderScan(folder_storage("cache/"), true) as $key => $value) {
        echo DataManager::delete($value) ? (cli_text_color(" DELETED: ", "yellow") . $value) : (cli_text_color(" NOT DELETED: ", "red") . $value);
        echo "\r\n";
    }
    fun_folder_denied(folder_storage("cache/"));
    echo cli_text_color("\r\n Clean!\r\n");
}

function fun_run_command(string $nameFile, $params = false)
{
    if ($params === false) {
        $params = [];
    } else if (!is_array($params)) {
        $params = [$params];
    }
    $folderCommandName = input_env("NAME_FOLDER_COMMANDS");
    $file = DataManager::path(__BASE_DIR__ . "/app/${folderCommandName}/${nameFile}.php");    
    if (DataManager::exist($file) == 'FILE') {
        (function() use ($file, $params) {
            try {
                require_once realpath(__DIR__ . "/../vendor/autoload.php");
                require_once realpath(__DIR__ . "/config.php");
                require_once $file;
                $count = workWait(function() { usleep(1); });
                echo PHP_EOL, "Meter: ";
                dumpl(\Lib\Meter::stop());
                if ($count > 0) {
                    echo PHP_EOL, "workRun has been run ${count} times", PHP_EOL;
                }
                die;
            } catch (\Throwable $th) {
                log_create($th);
                dumpd($th);
            }
        })();
    } else {
        echo PHP_EOL, "Command file not found: ", $file, PHP_EOL;
    }
}

if(!function_exists("readline")) {
    function readline($prompt = null) {
        if($prompt){
            echo $prompt;
        }
        $fp = fopen("php://stdin","r");
        $line = rtrim(fgets($fp, 1024));
        return $line;
    }
}

function confirmation_y_or_n(bool $auto_confirm = false)
{
    $y_or_n = "N";
    if (!$auto_confirm) {
        echo "\r\n####################################################";
        echo "\r\n# Confirm with ";
        echo cli_text_color("`YES`");
        echo " or ";
        echo cli_text_color("`Y`");
        echo " to continue (default is ";
        echo cli_text_color("`NOT`", "cyan");
        echo " or ";
        echo cli_text_color("`N`", "cyan");
        echo "): ";
        $y_or_n = mb_strtoupper(readline());
    } else {
        $y_or_n = "Y";
    }
    if ($y_or_n !== "Y" && $y_or_n !== "YES") {
        echo "# ";
        echo cli_text_color("Operation cancelled.", "cyan");
        echo "\r\n####################################################\r\n";
        die();
    } else {
        echo "# ";
        echo cli_text_color("Operation confirmed.");
        echo "\r\n####################################################\r\n";
    }
}

function fun_switch_app_options(string $cmd, string $nameFile, $require = false)
{
    $auto_confirm = false;
    if (
        (is_string($require) && $require !== "--yes" && $require !== "-y") ||
        (is_array($require) && !in_array("--yes", $require) && !in_array("-y", $require))
    ) {
        $auto_confirm = false;
    } else {
        $auto_confirm = true;
    }
    confirmation_y_or_n($auto_confirm);
    switch ($cmd) {
        case "controller":
            fun_create_controller($nameFile, !$require);
            break;
        case "middleware":
            fun_create_middleware($nameFile, !$require);
            break;
        case "service":
            fun_create_service($nameFile, $require);
            break;
        case "model":
            fun_create_model($nameFile, $require);
            break;
        case "helper":
            fun_create_helper($nameFile, $require);
            break;
        case "view":
            fun_create_view($nameFile);
            break;
        case "database":
            fun_apply_database($nameFile);
            break;
        case "command":
            fun_run_command($nameFile, $require);
            break;
    }
}

function fun_switch_other_options(string $cmd)
{
    confirmation_y_or_n();
    $attr = 80;
    if (preg_match("/:/", $cmd)) {
        $arr = explode(':', $cmd);
        if (count($arr) == 2) {
            $cmd = (string) $arr[0];
            $attr = (string) $arr[1];
        }
    }
    switch ($cmd) {
        case "server":
            fun_init_server($attr);
            break;
        case "test":
            fun_test_routes();
            break;
        case "route";
            if ($attr == 80) {
                $attr = "";
            }
            fun_routes($attr);
            break;
        case "clean":
            fun_clean_simple_mvcs();
            break;
        case "help":
            fun_list_commands();
            break;
        case "update":
            fun_update_project();
            break;
        case "nocache":
            fun_no_cache();
            break;
    }
}

function cli_text_color(string $text, string $color = "green", string $background = "black", bool $bold = true)
{
    // https://semanickz.wordpress.com/2020/03/27/linux-cor-colorindo-shell-script-cores/
    switch($background) {
        case "black":
            $background = 40;
            break;
        case "red":
            $background = 41;
            break;
        case "green":
            $background = 42;
            break;
        case "yellow":
            $background = 43;
            break;
        case "blue":
            $background = 44;
            break;
        case "purple":
            $background = 45;
            break;
        case "cyan":
            $background = 46;
            break;
        case "white":
            $background = 47;
            break;
        default:
            $background = 40;
    }    
    switch($color) {
        case "black":
            $color = 30;
            break;
        case "red":
            $color = 31;
            break;
        case "green":
            $color = 32;
            break;
        case "yellow":
            $color = 33;
            break;
        case "blue":
            $color = 34;
            break;
        case "purple":
            $color = 35;            
            break;       
        case "cyan":
            $color = 36;
            break;
        case "white":
            $color = 37;
            break;
        default:
            $color = 37;
    }
    $bold = (int) $bold;
    return "\e[${bold};${background};${color}m${text}\e[0m";
}
