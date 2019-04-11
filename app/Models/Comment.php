<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModel
{
    use SoftDeletes;

	protected $table = 'comments';
    protected $dates = ['deleted_at'];
    protected $fillable = ['member_id', 'instructor_id', 'comments'];
}
