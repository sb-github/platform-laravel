<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StopWord extends Model
{
    protected $table = 'stopword';

    protected $fillable = [
        'title',
        'crawler_id'
    ];
}
