### Environment Settings

> EN-US: The `.env` file must contain the project settings, such as: access to the database, password used to sign JWT tokens, generation of routes of the methods existing in the controllers, among other things

<hr>

### Configurações de Ambiente

> PT-BR: O arquivo `.env` deve conter as configurações do projeto, como: Acesso ao banco de dados, senha utilizada para assinar tokens JWT, geração de rotas dos métodos existentes nos controladores, dentre outras coisas

```env
# -----------------------------------------------------------------------------------------------------
# EN-US: Application base URL address
# PT-BR: Endereço de URL base do aplicativo
# -----------------------------------------------------------------------------------------------------
# APP_URL=http://localhost/makemvcss/

# -----------------------------------------------------------------------------------------------------
# EN-US: Current version number of the MakeMVCSS framework
# PT-BR: Número da versão atual do framework MakeMVCSS
# -----------------------------------------------------------------------------------------------------
VERSION=4.5.0

# -----------------------------------------------------------------------------------------------------
# EN-US: Defines the timezone (GMT) to be used by the PHP application and Database
# PT-BR: Define o fuso horário (GMT) a ser usado pela aplicação PHP e Banco de Dados
# -----------------------------------------------------------------------------------------------------
# TIMEZONE=UTC
TIMEZONE=America/Sao_Paulo

# -----------------------------------------------------------------------------------------------------
# EN-US: Defines the main language to be used for internationalization (I18N)
# PT-BR: Define o idioma principal a ser usado para internacionalização (I18N)
# -----------------------------------------------------------------------------------------------------
MAIN_LANGUAGE=en-us
# MAIN_LANGUAGE=pt-br

# -----------------------------------------------------------------------------------------------------
# EN-US:
#   Indicates the environment in which the software is to be run
#   The environments are: development, test or production
# PT-BR:
#   Indica o ambiente no qual o software deve ser executado
#   Os ambientes são: development, test or production (desenvolvimento, teste ou produção)
# -----------------------------------------------------------------------------------------------------
ENV=development

# -----------------------------------------------------------------------------------------------------
# EN-US: Automatically generate the routes that lead to the view
# PT-BR: Gera automaticamente as rotas que levam para a view
# -----------------------------------------------------------------------------------------------------
AUTO_VIEW_ROUTE=true

# -----------------------------------------------------------------------------------------------------
# EN-US: Generate only routes from controllers signed by `private $generateRoutes;`
# PT-BR: Gerar somente as rotas dos controladores assinados por `private $generateRoutes;`
# -----------------------------------------------------------------------------------------------------
GENERATE_SIGNED_CONTROLLER_ROUTES_ONLY=true

# -----------------------------------------------------------------------------------------------------
# EN-US: Definition of the name of each folder that represents the MVCSS model
# PT-BR: Definição do nome de cada pasta que representa o modelo MVCSS
# -----------------------------------------------------------------------------------------------------
NAME_FOLDER_VIEWS=Views
NAME_FOLDER_MODELS=Models
NAME_FOLDER_HELPERS=Helpers
NAME_FOLDER_SCHEMAS=Schemas
NAME_FOLDER_SERVICES=Services
NAME_FOLDER_CONTROLLERS=Controllers
NAME_FOLDER_MIDDLEWARES=Middlewares

# -----------------------------------------------------------------------------------------------------
# EN-US: Defines the application entry point, can be a View or a Controller@method
# PT-BR: Define o ponto de entrada da aplicação, pode ser uma View ou um Controller@method
# -----------------------------------------------------------------------------------------------------
# INIT_ACTION_APP=HomeController@index

# -----------------------------------------------------------------------------------------------------
# EN-US: After validating a CSRF token, a new token must be generated
# PT-BR: Após a validação de um token CSRF, um novo token deve ser gerado
# -----------------------------------------------------------------------------------------------------
CSRF_REGENERATE=false

# -----------------------------------------------------------------------------------------------------
# EN-US: Password used in the encryption and decryption of texts using the class `AES_256`
# PT-BR: Senha usada na criptografia e descriptografia de textos utilizando a classe `AES_256`
# -----------------------------------------------------------------------------------------------------
AES_256_SECRET=password12345

# -----------------------------------------------------------------------------------------------------
# EN-US: Password used to sign the JSON Web Token
# PT-BR: Senha utilizada para assinar token JSON Web Token
# -----------------------------------------------------------------------------------------------------
JWT_SECRET=password12345

# -----------------------------------------------------------------------------------------------------
# EN-US: Configuration for Microsoft SQL Server
# PT-BR: Configuração para Microsoft SQL Server
# -----------------------------------------------------------------------------------------------------
# DB_CONNECTION=sqlsrv
# DB_DATABASE=database
# DB_HOST=localhost
# DB_PORT=1433
# DB_USERNAME=root
# DB_PASSWORD=123456

# -----------------------------------------------------------------------------------------------------
# EN-US: Configuration for PostgreSQL
# PT-BR: Configuração para PostgreSQL
# -----------------------------------------------------------------------------------------------------
# DB_CONNECTION=pgsql
# DB_DATABASE=database
# DB_HOST=localhost
# DB_PORT=5432
# DB_USERNAME=root
# DB_PASSWORD=123456

# -----------------------------------------------------------------------------------------------------
# EN-US: Configuration for MySQL or MariaDB
# PT-BR: Configuração para MySQL ou MariaDB
# -----------------------------------------------------------------------------------------------------
# DB_CONNECTION=mysql
# DB_DATABASE=database
# DB_HOST=localhost
# DB_PORT=3306
# DB_USERNAME=root
# DB_PASSWORD=
# DB_CHARSET=utf8
# DB_CHARSET_COLLATE=utf8_unicode_ci
# DB_PREFIX=

# -----------------------------------------------------------------------------------------------------
# EN-US: Configuration for SQLite
# PT-BR: Configuração para SQLite
# -----------------------------------------------------------------------------------------------------
DB_CONNECTION=sqlite
DB_DATABASE=database
```

### [Back to the previous page](./DOC-EU.md) | [Voltar para página anterior](./DOC.md)
