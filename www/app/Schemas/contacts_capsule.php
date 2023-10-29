<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('contacts');

Capsule::schema()->create('contacts', function ($table) {

    //$table->increments('id');

    $table->uuid('uuid')->primary();

    $table->string('name');

    $table->string('email')->unique();

    $table->timestamps();

});
