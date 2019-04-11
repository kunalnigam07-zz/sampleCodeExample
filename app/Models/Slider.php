<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends BaseModel
{
    use SoftDeletes;

	protected $table = 'sliders';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'img_left', 'img_right', 'ordering', 'status'];
}
