<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends BaseModel
{
    use SoftDeletes;

	protected $table = 'countries';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'code', 'dialing', 'sanctioned', 'status'];
}
