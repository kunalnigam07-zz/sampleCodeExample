<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Interest extends BaseModel
{
    use SoftDeletes;

	protected $table = 'interests';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'class_type_id'];
}
