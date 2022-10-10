# [Continuum documentation in Brazilian Portuguese](./DOC.md)

## <a href="https://github.com/matheusjohannaraujo/continuum/">Continuum</a> / <a href="https://continuum-framework.herokuapp.com">Demonstration Online</a> / <a href="https://www.youtube.com/playlist?list=PLODC80noz2kLRlieO38YwqaJXuzevAO83">Youtube Playlist</a>

> ### Summary / History:

>> #### Continuum is a PHP framework based on several frameworks already on the market for developing web applications that follow the MVC standard

>> #### When I started to develop this project, I had no idea to build something so complete and complex, but only a system that would help me to form friendly URLs to be easily interpreted by a route system. As I saw that it was possible to improve the project and leave it with more functionality, I dedicated myself to producing something that could be used in place of existing frameworks on the market

>> #### This project made me learn and develop several more complex knowledge in the PHP language, such as: namespaces, autoload, passing by value and by reference, propagation operator, anonymous functions, data types, composer, reflection, HTTP verbs, command line script, environment variables, authentication and authorization, CORS, CSRF, JSON, JWT, REST, design and design standards, clean code and writing documentation through Markdown

>> #### During the development of the project I used Laravel, Codeigniter and ASP .NET Core as a base

>> #### In structuring the framework, I followed the existing model in Laravel where the entire system (application) is built inside the `app` folder. And I looked at the Codeigniter structure to find a model that would allow me to develop a lean, light and simple project

>> #### In the automatic mapping of the existing methods in the Controller, I used the ASP .NET Core operating mode as a base

<hr>

> ### Features / Functionalities:

>> #### Follows the MVCSS structure pattern (Model, View, Controller, Scheme and Service);

>> #### It has a route system with a friendly URL;

>> #### It allows to work with REST through several HTTP methods, such as: GET, POST, PUT, DELETE, PATCH and OPTIONS;

>> #### Accepts requests through CORS (Resource sharing with different origins), which may contain JSON, FormData and x-www-form-urlencoded in the request body;

>> #### It has an automatic generator of controller routes signed by: `private $generateRoutes`

>> #### Contains the CSRF Token generation and validation functionality for HTTP requests;

>> #### It has a class for issuing and validating JWT (JSON Web Token) natively;

>> #### Generates tables in the database from a schema and maps it through a model;

>> #### The databases supported by Eloquent ORM are: MySQL, MariaDB, PostgreSQL, SQLite and SQL Server;

>> #### Attention: This Framework was built to be used on Apache server, however it works on Nginx and IIS.

>> ```
>> The framework uses the Apache Server rewrite through HTACCESS,
>> on IIS and Nginx servers the framework may not work correctly.
>> ```

<hr>

### [Requirements](./Requirements.md)

> #### Contains information about the settings that must exist for the project to work correctly

<hr>

### [Running Commands](./RunningCommands.md)

> #### In the Continuum project folder, there is a file called `adm`, with which you can execute commands that perform some actions

> #### Allows the generation of controllers, visualizations, services, models and other things

<hr>

### [Global Helpers](./GlobalHelpers.md)

> #### The file in `lib/helpers.php` contains the implementation of global helpers (functions), which can be used anywhere in the framework

<hr>

## Examples below of how to use the MVCSS framework

### Defining public files
> Inside the `public` folder is the place where you should store public files (assets), such as CSS, JS and IMG

### Defining private files
> Inside the `app` private folder is where the files that make up the application should be stored, such as Routes, Model, Views, Controllers, Schemes and Services

> Inside the private folder `storage` is where the attachment files (uploads) should be stored

### [Schemas and Models](./SchemasAndModels-EU.md)

> #### Allows you to model and access the database

> #### To use Eloquent ORM, it is necessary to install its dependencies. Below are the installation and uninstallation commands:

>> **`composer db-require`**, **`composer db-remove`**

### [Defining Services](./DefiningServices.md)

> #### It is used to make the application's business rule, such as data validations sent to the controller

### [Defining Controllers](./DefiningControllers.md)

> #### Contains the implementation of the controller's methods, with the possibility of generating automatic routes

### [Defining Routes](./DefiningRoutes.md)

> #### Allows the creation of routes manually, which lead to a method in the controller, which returns a display or has its own function scope (callback)

### [Defining Templates and Views](./DefiningTemplatesAndViews.md)

> #### It is used to create screens, which can be static or dynamic. These screens can be used as `View` or `Templates`

### [Environment Settings](./EnvironmentSettings.md)

> #### The `.env` file must contain the project settings, such as: access to the database, password used to sign JWT tokens, generation of routes of the methods existing in the controllers, among other things
