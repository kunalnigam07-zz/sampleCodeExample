<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Ban extends BaseModel
{
    use SoftDeletes;

	protected $table = 'bans';
    protected $dates = ['deleted_at'];
    protected $fillable = ['ip', 'email', 'bank_account_number', 'paypal_email', 'notes', 'status'];
}
