# [MakeMVCSS documentação em Inglês](./README-EU.md)

## <a href="https://matheusjohannaraujo.herokuapp.com">Matheus Johann Araújo</a>

> ##### Pais: Brasil

> ##### Estado: Pernambuco

> ##### Data: 24/01/2021

## <a href="https://github.com/matheusjohannaraujo/makemvcss/">MakeMVCSS</a> / <a href="https://makemvcss.herokuapp.com">Demostração Online</a> / <a href="https://www.youtube.com/playlist?list=PLODC80noz2kLRlieO38YwqaJXuzevAO83">Playlist Youtube</a>

> ### Resumo / História:

>> #### O MakeMVCSS é um framework PHP baseado em diversos frameworks já existentes no mercado para desenvolvimento de aplicações web que seguem o padrão MVC

>> #### Quando comecei a desenvolver este projeto, não tinha como ideia construir algo tão completo e complexo, e sim somente um sistema que me auxiliasse a formar URLs amigáveis para serem interpretadas facilmente por um sistema de rotas. Como vi que era possível aprimorar o projeto e deixa-lo com mais funcionalidades, dediquei-me a produzir algo que pudesse ser utilizado no lugar de frameworks já existentes no mercado

>> #### Este projeto me fez aprender e desenvolver vários conhecimentos mais complexos na linguagem PHP, como: namespaces, carregamento automático (Autoload), passagem por valor e por referência, operador de propagação, funções anônimas, tipos de dados, compositor (Composer), reflexão (Reflection), verbos HTTP, script de linha de comando (CLI), variáveis de ambiente (ENV), autenticação e autorização, CORS, CSRF, JSON, JWT, REST, padrões de design e de projeto, código limpo e escrita de documentação através do Markdown

>> #### Durante o desenvolvimento do projeto levei como base o Laravel, Codeigniter e ASP .NET Core

>> #### Na estruturação do framework, segui o modelo existente no Laravel onde toda sistema (aplicação) é construída dentro da pasta `app`. E observei a estrutura do Codeigniter para encontrar um modelo que me permitisse desenvolver um projeto enxuto, leve e simples

>> #### No mapeamento automático dos métodos existentes no Controller utilizei como base o modo de funcionamento do ASP .NET Core

<hr>

> ### Características / Funcionalidades:

>> #### Segue o padrão de estrutura MVCSS (Modelo, Visão, Controlador, Esquema e Serviço);

>> #### Contém um sistema de rotas com URL amigável;

>> #### Permite trabalhar com REST através de vários métodos HTTP, como: GET, POST, PUT, DELETE, PATCH e OPTIONS;

>> #### Aceita requisições através de CORS (Compartilhamento de recursos com origens diferentes), podendo conter JSON, FormData e x-www-form-urlencoded no corpo da requisição;

>> #### Tem um gerador automático de rotas do controlador assinadas por: `private $generateRoutes`

>> ####	Contém a funcionalidade de geração e validação de Token CSRF para requisições HTTP;

>> #### Possui uma classe para emissão e validação de JWT (JSON Web Token) de forma nativa;

>> #### Gera tabelas no banco de dados a partir de um esquema e mapeia-o através de um modelo;

>> #### Os bancos de dados suportados pelo Eloquent ORM são: MySQL, MariaDB, PostgreSQL, SQLite e SQL Server

>> #### Atenção: Este Framework foi construído para ser utilizado em servidor Apache, porém funciona em Nginx e IIS.

>> ```
>> O framework usa a reescrita (ReWrite) do Apache Server através do HTACCESS, em servidores
>> Nginx e IIS algumas das funcionalidades presentes no HTACCESS podem não funcionar.
>> ```

<hr>

### [Exigências](./Requirements.md)

> #### Informações sobre as configurações que devem existir para que o projeto funcione corretamente

<hr>

### [Executando Comandos](./RunningCommands.md)

> #### Dentro da pasta do projeto MakeMVCSS existe um arquivo chamado `adm`, com ele é possível executar comandos que realizam algumas ações

> #### Permite a geração de Controladores, Visões, Serviços, Modelos e outras coisas

<hr>

### [Ajudantes Globais](./GlobalHelpers.md)

> #### O arquivo em `lib/helpers.php` contém a implementação de ajudantes (funções) globais, que podem ser utilizadas em qualquer lugar do framework

<hr>

## Exemplos abaixo de como usar a estrutura MVCSS

### Definindo arquivos públicos

> Dentro da pasta `public` é o local onde você deve armazenar os arquivos públicos, como CSS, JS e IMG

### Definindo arquivos particulares

> Dentro da pasta privada `app` é onde os arquivos que compõem o aplicativo devem ser armazenados, como Rotas, Modelo, Visões, Controladores, Esquemas e Serviços

> Dentro da pasta privada `storage` é onde os arquivos anexos (uploads) devem ser armazenados

### [Esquemas e Modelos](./SchemasAndModels.md)

> #### Permite fazer a modelagem e acesso ao banco de dados

> #### Para usar o Eloquent ORM, é necessário instalar suas dependências. Abaixo estão os comandos de instalação e desinstalação:

>> **`composer db-require`**, **`composer db-remove`**

### [Definindo Serviços](./DefiningServices.md)

> #### É utilizado para fazer a lógica da regra de negócios da aplicação, como validações de dados submetidos ao controlador

### [Definindo Controladores](./DefiningControllers.md)

> #### Contém a implementação dos métodos do controlador, podendo ter geração de rotas automáticas

### [Definindo Rotas](./DefiningRoutes.md)

> #### Possibilita a criação de rotas de maneira manual, que direcionam a um método no controlador, visão ou possui um escopo de função (callback) próprio

### [Definindo Templates e Visões](./DefiningTemplatesAndViews.md)

> #### É utilizado para criação de telas, podendo ser estáticas ou dinâmicas. Essas telas podem ser usadas como `View` (Visão) ou `Templates` (Modelo)

### [Configurações de Ambiente](./EnvironmentSettings.md)

> #### O arquivo `.env` deve conter as configurações do projeto, como: Acesso ao banco de dados, senha utilizada para assinar tokens JWT, geração de rotas dos métodos existentes nos controladores, dentre outras coisas
