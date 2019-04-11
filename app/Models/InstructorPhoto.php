<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorPhoto extends BaseModel
{
    use SoftDeletes;

	protected $table = 'instructors_photos';
    protected $dates = ['deleted_at'];
    protected $fillable = ['img', 'user_id', 'ordering', 'status'];
}
