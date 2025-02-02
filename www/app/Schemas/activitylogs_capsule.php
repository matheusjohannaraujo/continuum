<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('activity_logs');

Capsule::schema()->create('activity_logs', function ($table) {
    $table->bigIncrements('id');
    $table->string('model_type');    // Classe do model (ex: App\Models\User)
    $table->unsignedBigInteger('model_id')->nullable(); // ID do registro
    $table->string('event');         // Ex: created, updated, deleted
    $table->longText('old_values')->nullable(); // Valores antigos em JSON
    $table->longText('new_values')->nullable(); // Valores novos em JSON
    $table->unsignedBigInteger('caused_by')->nullable(); // ID do usuário que causou
    $table->timestamps();
});
