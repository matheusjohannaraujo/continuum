<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ActivityLog extends Eloquent
{

    protected $table = 'activity_logs';

    protected $fillable = [
        'model_type',
        'model_id',
        'event',
        'old_values',
        'new_values',
        'caused_by'
    ];
}
