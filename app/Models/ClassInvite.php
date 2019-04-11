<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClassInvite extends BaseModel
{
    use SoftDeletes;

	protected $table = 'classes_invites';
    protected $dates = ['deleted_at'];
    protected $fillable = ['class_id', 'list_id', 'name', 'email', 'opens', 'clicks', 'booked', 'guid'];

    // Relationships
    public function classEvent()
    {
        return $this->belongsTo('App\Models\ClassEvent', 'class_id');
    }
}
