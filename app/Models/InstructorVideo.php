<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorVideo extends BaseModel
{
    use SoftDeletes;

	protected $table = 'instructors_videos';
    protected $dates = ['deleted_at'];
    protected $fillable = ['url', 'user_id', 'ordering', 'status'];
}
