<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'skill_id',
        'text',
        'title'
    ];
    
        public function skill(){
        return $this->belongsTo('App\Skill');
    }
}
