<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends BaseModel
{
    use SoftDeletes;

	protected $table = 'faqs';
    protected $dates = ['deleted_at'];
    protected $fillable = ['question', 'answer', 'category_id', 'ordering', 'status'];

    public function category()
    {
        return $this->belongsTo('App\Models\FAQCategory');
    }
}
