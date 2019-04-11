<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class HIW extends BaseModel
{
    use SoftDeletes;

	protected $table = 'hiw';
    protected $dates = ['deleted_at'];
    protected $fillable = ['img', 'contents', 'ordering', 'category_id', 'status'];
}
