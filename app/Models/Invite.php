<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invite extends BaseModel
{
    use SoftDeletes;

	protected $table = 'invites';
    protected $dates = ['deleted_at'];
    protected $fillable = ['sender_name', 'sender_email', 'sender_id', 'class_id', 'ip', 'friends'];
}
