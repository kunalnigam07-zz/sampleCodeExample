<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FAQCategory extends BaseModel
{
    use SoftDeletes;

	protected $table = 'faqs_categories';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'ordering', 'section_id', 'status'];

    public function topic()
    {
        return $this->belongsTo('App\Models\FAQTopic');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\FAQ', 'category_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->faqs()->delete();
        });
    }
}
