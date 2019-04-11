<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClassType extends BaseModel
{
    use SoftDeletes;

	protected $table = 'classes_types';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'parent_id', 'ordering', 'status'];
}
