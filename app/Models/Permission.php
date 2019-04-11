<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Baum\Node;

class Permission extends Node 
{
    use SoftDeletes;

    protected $table = 'permissions';

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }
}
