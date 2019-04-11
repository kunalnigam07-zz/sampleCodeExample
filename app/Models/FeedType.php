<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FeedType extends BaseModel
{
    use SoftDeletes;

	protected $table = 'feed_types';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'section_id', 'for_users', 'template', 'show_timestamp', 'ordering', 'icon'];
}
