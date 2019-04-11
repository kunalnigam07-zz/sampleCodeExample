<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClassEquipment extends BaseModel
{
    use SoftDeletes;

	protected $table = 'classes_equipment';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'ordering', 'class_id', 'status'];
}
