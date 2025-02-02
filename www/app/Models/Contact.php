<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Contact extends Eloquent
{

    use LogsActivity;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $table = 'contacts';

    protected $fillable = [
        'uuid',
        'name',
        'email'
    ];
}
