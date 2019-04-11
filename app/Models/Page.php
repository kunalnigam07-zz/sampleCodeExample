<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BaseModel
{
    use SoftDeletes;

	protected $table = 'pages';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'heading', 'brief', 'url', 'ordering', 'grouping', 'contents', 'code_head', 'code_footer', 'status'];
}
