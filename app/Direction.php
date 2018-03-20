<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table = 'directions';
    protected $hidden = array('pivot');
    protected $fillable = [
        'title',
        'image',
        'parent',
        'main_skill_id'
    ];

    public function skills()
    {
        return $this->belongsToMany('App\Skill');
    }

    public function subdirections()
    {
        return $this->hasMany('App\Direction', 'parent');
    }
}
