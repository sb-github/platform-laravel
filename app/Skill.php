<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


/**
 * @property mixed request
 */
class Skill extends Model
{
    protected $table = 'skills';

    protected $fillable = ['title','image'];



    public function Group() {
        return $this->belongsToMany('App\Group');
    }

    public function User() {
        return $this->belongsToMany('App\User');
    }

}
