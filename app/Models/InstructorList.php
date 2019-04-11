<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorList extends BaseModel
{
    use SoftDeletes;

	protected $table = 'instructors_lists';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'user_id', 'status'];

    public function listUsersPivot()
    {
        return $this->hasMany('App\Models\InstructorListUser', 'list_id');
    }

    public function listOwner()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->listUsersPivot()->delete();
        });
    }
}
