<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StopWord extends Model
{
    protected $table = 'stop_words';

    protected $fillable = [
        'title'
    ];
}
