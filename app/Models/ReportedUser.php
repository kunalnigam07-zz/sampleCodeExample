<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ReportedUser extends BaseModel
{
    use SoftDeletes;

	protected $table = 'reported_users';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'reported_user_id', 'reason', 'notes', 'status'];

    // Relationships
    public function reportingMember()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function reportedMember()
    {
        return $this->belongsTo('App\Models\User', 'reported_user_id');
    }
}
