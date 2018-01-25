<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


/**
 * @property mixed request
 */
class Skill extends Model
{
    protected $table = 'skills';

    protected $fillable = [
        'title',
        'image'
    ];

    protected $hidden = array('pivot');

    public function direction() {
        return $this->belongsToMany('App\Direction');
    }
    public function materials(){
        return $this->hasMany('App\Material');
    }
}
