<?php

namespace App\Models;

class PasswordReset extends BaseModel
{
	protected $table = 'password_resets';
    protected $fillable = ['email', 'token', 'created_at'];
    public $timestamps = false;
}
