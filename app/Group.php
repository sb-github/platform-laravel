<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	protected $table = 'groups';

    protected $fillable = [
        'title',
        'image'
    ];
	
    public function Skill() {
        return $this->belongsToMany('App\Skill', 'group_skills');
    }
}
