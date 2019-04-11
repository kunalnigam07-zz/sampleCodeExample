<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends BaseModel
{
    use SoftDeletes;

	protected $table = 'feed';
    protected $dates = ['deleted_at'];
    protected $fillable = ['type_id', 'user_id', 'data', 'url', 'instructor_id', 'class_id', 'expires_at'];

    public function feedType()
    {
        return $this->belongsTo('App\Models\FeedType', 'type_id');
    }

    public function instructor()
    {
        return $this->belongsTo('App\Models\User', 'instructor_id');
    }
}
