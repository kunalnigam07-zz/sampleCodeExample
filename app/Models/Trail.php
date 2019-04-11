<?php

namespace App\Models;

class Trail extends BaseModel
{
	protected $table = 'trails';
    protected $fillable = ['user_id', 'type', 'ip', 'details'];
}
