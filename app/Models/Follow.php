<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Follow extends BaseModel
{
    use SoftDeletes;

	protected $table = 'follows';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'followed_by_id'];
}
