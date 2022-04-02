<?php 

$this->section("title", "MakeMVCSS - Matheus Johann Araújo");
$this->extends("layouts.html5");
$this->section("body");

?>

        <h4>Route to list all routes</h4>

        <span class="badge badge-pill badge-primary">
            Works only in development environment
        </span>

        <div class="list-group mt-3">
            <span class="list-group-item bg-dark text-white">RETURN HTML DUMP</span>
            <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all"); ?>"><b>/routes/all</b></a>
            <a class="list-group-item list-group-item-action bg-warning" href="<?= site_url("routes/all/any"); ?>"><b>/routes/all/any</b></a>
            <a class="list-group-item list-group-item-action bg-primary text-white" href="<?= site_url("routes/all/get"); ?>"><b>/routes/all/get</b></a>
            <a class="list-group-item list-group-item-action bg-success text-white" href="<?= site_url("routes/all/post"); ?>"><b>/routes/all/post</b></a>
            <a class="list-group-item list-group-item-action bg-secondary text-white" href="<?= site_url("routes/all/put"); ?>"><b>/routes/all/put</b></a>
            <a class="list-group-item list-group-item-action bg-info text-white" href="<?= site_url("routes/all/patch"); ?>"><b>/routes/all/patch</b></a>
            <a class="list-group-item list-group-item-action bg-danger text-white" href="<?= site_url("routes/all/delete"); ?>"><b>/routes/all/delete</b></a>
        </div>

        <div class="list-group mt-3">
            <span class="list-group-item bg-dark text-white">RETURN TEXT JSON</span>
            <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json"); ?>"><b>/routes/all/json</b></a>
            <a class="list-group-item list-group-item-action bg-warning" href="<?= site_url("routes/all/json/any"); ?>"><b>/routes/all/json/any</b></a>
            <a class="list-group-item list-group-item-action bg-primary text-white" href="<?= site_url("routes/all/json/get"); ?>"><b>/routes/all/json/get</b></a>
            <a class="list-group-item list-group-item-action bg-success text-white" href="<?= site_url("routes/all/json/post"); ?>"><b>/routes/all/json/post</b></a>
            <a class="list-group-item list-group-item-action bg-secondary text-white" href="<?= site_url("routes/all/json/put"); ?>"><b>/routes/all/json/put</b></a>
            <a class="list-group-item list-group-item-action bg-info text-white" href="<?= site_url("routes/all/json/patch"); ?>"><b>/routes/all/json/patch</b></a>
            <a class="list-group-item list-group-item-action bg-danger text-white" href="<?= site_url("routes/all/json/delete"); ?>"><b>/routes/all/json/delete</b></a>
        </div>

        <div class="list-group mt-3 mb-3">
            <span class="list-group-item bg-dark text-white">EXAMPLE OF ROUTES - WITH HTTP METHOD</span>
            <a class="list-group-item list-group-item-action" href="<?= site_url("template"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/template</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("json"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/json</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("auth"); ?>"><span class="badge badge-warning mr-3">ANY</span><b>/auth</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("jwt"); ?>"><span class="badge badge-warning mr-3">ANY</span> <b>/jwt</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("math/add/3/5"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/math/add/3/5</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("api/v1/text"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/api/v1/text</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("api/v1/video"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/api/v1/video</b></a>
            <a class="list-group-item list-group-item-action" href="<?= site_url("api/v1/video/stream"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/api/v1/video/stream</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.index"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/contact/index</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.index_raw"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/contact/index_raw</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.new"); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/contact/new</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.create"); ?>"><span class="badge badge-success mr-3">POST</span> <b>/contact/create</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.show", 1); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/contact/show/{id:int}</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.edit", 1); ?>"><span class="badge badge-primary mr-3">GET</span> <b>/contact/edit/{id:int}</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.update", 1); ?>"><span class="badge badge-secondary mr-3">PUT</span> <b>/contact/update/{id:int}</b></a>
            <a class="list-group-item list-group-item-action" href="<?= route("contact.destroy", 1); ?>"><span class="badge badge-danger mr-3">DELETE</span> <b>/contact/destroy/{id:int}</b></a>
        </div>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" id="show_info_framework" style="display: none;" data-toggle="modal" data-target="#exampleModalLong">
            Launch demo modal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">MakeMVCSS Framework</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card" style="width: auto;">
                            <?= tag_img("index.jpeg", ["class" => "card-img-top", "id" => "autor", "title" => "MakeMVCSS author: Matheus Johann Araújo", "alt" => "MakeMVCSS author: Matheus Johann Araújo"]); ?>
                            <div class="card-body">
                                <h5 class="card-title">Hello, my name is Matheus Johann Araújo</h5>
                                <p class="card-text text-justify"><i>The <b><a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank">MakeMVCSS</a></b> is a PHP framework based on several frameworks already on the market for developing web applications that follow the MVC standard</i></p>
                                <div>
                                    <a href="https://github.com/matheusjohannaraujo/makemvcss" target="_blank" class="btn btn-primary">GitHub</a>
                                    <button class="ml-3 btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        Read more/less
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="collapseExample">
                                    <p class="card-text text-justify">When I started to develop this project, I had no idea to build something so complete and complex, but only a system that would help me to form friendly URLs to be easily interpreted by a route system. As I saw that it was possible to improve the project and leave it with more functionality, I dedicated myself to producing something that could be used in place of existing frameworks on the market</p>
                                    <p class="card-text text-justify">This project made me learn and develop several more complex knowledge in the PHP language, such as: namespaces, autoload, passing by value and by reference, propagation operator, anonymous functions, data types, composer, reflection, HTTP verbs, command line script, environment variables, authentication and authorization, CORS, CSRF, JSON, JWT, REST, design and design standards, clean code and writing documentation through Markdown</p>
                                    <p class="card-text text-justify">During the development of the project I used Laravel, Codeigniter and ASP .NET Core as a base</p>
                                    <p class="card-text text-justify">In structuring the framework, I followed the existing model in Laravel where the entire system (application) is built inside the `app` folder. And I looked at the Codeigniter structure to find a model that would allow me to develop a lean, light and simple project</p>
                                    <p class="card-text text-justify">In the automatic mapping of the existing methods in the Controller, I used the ASP .NET Core operating mode as a base</p>
                                </div>                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php $this->endSection(); ?>
