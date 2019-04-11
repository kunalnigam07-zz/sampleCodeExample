<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorListUser extends BaseModel
{
    use SoftDeletes;

	protected $table = 'instructors_lists_users';
    protected $dates = ['deleted_at'];
    protected $fillable = ['list_id', 'name', 'email'];
}
