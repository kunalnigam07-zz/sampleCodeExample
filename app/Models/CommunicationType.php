<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationType extends BaseModel
{
    use SoftDeletes;

	protected $table = 'communication_types';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title_member', 'title_instructor', 'status'];
}
