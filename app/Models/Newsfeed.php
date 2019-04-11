<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Newsfeed extends BaseModel
{
    use SoftDeletes;

	protected $table = 'newsfeed';
    protected $dates = ['deleted_at'];
    protected $fillable = ['section_id', 'for_users', 'url', 'ordering', 'img', 'contents', 'status'];
}
