<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Grid extends BaseModel
{
    use SoftDeletes;

	protected $table = 'grid';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'img', 'ordering', 'status'];
}
