<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Contact extends Eloquent
{

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $table = 'contacts';

    protected $fillable = [
        'uuid',
        'name',
        'email'
    ];

}
