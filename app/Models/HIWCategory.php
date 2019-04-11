<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class HIWCategory extends BaseModel
{
    use SoftDeletes;

	protected $table = 'hiw_categories';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'ordering', 'section_id', 'status'];

    public function hiws()
    {
        return $this->hasMany('App\Models\HIW', 'category_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->hiws()->delete();
        });
    }
}
