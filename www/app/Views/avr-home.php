<?php

$this->section("title", "Continuum Framework");
$this->extends("layouts.html5");
$this->section("body");

?>       

        <div style="max-width: 900px;" class="mx-auto mb-5">
            <div class="mb-3" style="text-align: right;">
                <?= tag_img("logo.png"); ?>
            </div>
            <div class="mb-3">
                <?= tag_img("logo.png"); ?>
            </div>
            <div class="h1 text-center text-primary">
                A simple and complete PHP framework, thought and designed by Matheus Johann Araújo
            </div>
            <div class="text-center mx-auto" style="max-width: 300px;">
                <a target="_blank" href="https://github.com/matheusjohannaraujo/continuum" class="btn btn-primary mt-5 p-3 pt-2 pb-2 w-100" style="font-size: 1.7em;">
                    DOCUMENTATION
                </a>
            </div>
            <div class="text-left mx-auto pt-5" style="max-width: 600px;">
                <?= tag_img("logo.png"); ?>
            </div>            
        </div>

        <div class="container pt-4">

            <h4>LIST ALL ROUTES</h4>

            <span class="badge bg-primary">Works only in development environment</span>

            <div class="row mb-3">
                <div class="col">
                    <div class="list-group mt-3">
                        <span class="list-group-item bg-primary text-white fw-bold text-center mb-3 rounded">RETURN HTML DUMP</span>
                        <a class="list-group-item list-group-item-action bg-light rounded-top border" href="<?= site_url("routes/all"); ?>"><span class="badge bg-dark mr-3">ALL</span> <b>/routes/all</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/any"); ?>"><span class="badge bg-info mr-3">ANY</span> <b>/routes/all/any</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/get"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/routes/all/get</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/post"); ?>"><span class="badge bg-success mr-3">POST</span> <b>/routes/all/post</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/put"); ?>"><span class="badge bg-secondary mr-3">PUT</span> <b>/routes/all/put</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/patch"); ?>"><span class="badge bg-secondary mr-3">PUT</span> <b>/routes/all/patch</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/delete"); ?>"><span class="badge bg-danger mr-3">DELETE</span> <b>/routes/all/delete</b></a>
                    </div>
                </div>
                <div class="col">
                    <div class="list-group mt-3">
                        <span class="list-group-item bg-primary text-white fw-bold text-center mb-3 rounded">RETURN TEXT JSON</span>
                        <a class="list-group-item list-group-item-action bg-light rounded-top border" href="<?= site_url("routes/all/json"); ?>"><span class="badge bg-dark mr-3">ALL</span> <b>/routes/all/json</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/any"); ?>"><span class="badge bg-info mr-3">ANY</span> <b>/routes/all/json/any</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/get"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/routes/all/json/get</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/post"); ?>"><span class="badge bg-success mr-3">POST</span> <b>/routes/all/json/post</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/put"); ?>"><span class="badge bg-secondary mr-3">PUT</span> <b>/routes/all/json/put</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/patch"); ?>"><span class="badge bg-secondary mr-3">PUT</span> <b>/routes/all/json/patch</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("routes/all/json/delete"); ?>"><span class="badge bg-danger mr-3">DELETE</span> <b>/routes/all/json/delete</b></a>
                    </div>
                </div>
            </div>        

            <span class="list-group-item bg-primary text-white fw-bold text-center rounded">EXAMPLE OF ROUTES - WITH HTTP METHOD</span>
            <div class="row mb-3">     
                <div class="col mt-3">
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action bg-light" href="<?= route("contact.index"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/contact/index</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= folder_public("swagger-ui/"); ?>"><span class="badge bg-dark mr-3">Swagger UI</span> <b>Contact</b></a>
                    </div>
                </div>
                <div class="col mt-3">
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("api/v1/text"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/api/v1/text</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("api/v1/video"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/api/v1/video</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("api/v1/video/stream"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/api/v1/video/stream</b></a>
                    </div>
                </div>       
                <div class="col mt-3">
                    <div class="list-group">                    
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("template"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/template</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("json"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/json</b></a>
                        <a class="list-group-item list-group-item-action bg-light" href="<?= site_url("math/add/3/5"); ?>"><span class="badge bg-primary mr-3">GET</span> <b>/math/add/3/5</b></a>
                    </div>
                </div>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" id="show_info_framework" style="display: none;">
                Launch demo modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Continuum Framework</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card" style="width: auto;">
                                <?= tag_img("index.jpeg", ["class" => "card-img-top", "id" => "autor", "title" => "Continuum author: Matheus Johann Araújo", "alt" => "Continuum author: Matheus Johann Araújo"]); ?>
                                <div class="card-body">
                                    <h5 class="card-title">Hello, my name is Matheus Johann Araújo</h5>
                                    <p class="card-text text-justify"><i>The <b><a href="https://github.com/matheusjohannaraujo/continuum" target="_blank">Continuum</a></b> is a PHP framework based on several frameworks already on the market for developing web applications that follow the MVC standard</i></p>
                                    <div>
                                        <a href="https://github.com/matheusjohannaraujo/continuum" target="_blank" class="btn btn-primary">GitHub</a>
                                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
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

        </div>        

<?php $this->endSection(); ?>
