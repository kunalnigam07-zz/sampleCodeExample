<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationSetting extends BaseModel
{
    use SoftDeletes;

	protected $table = 'communication_settings';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'type_id', 'email', 'sms'];
}
