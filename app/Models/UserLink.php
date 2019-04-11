<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserLink extends BaseModel
{
    use SoftDeletes;

	protected $table = 'users_links';
    protected $dates = ['deleted_at'];
    protected $fillable = ['instructor_id', 'member_id'];
}
