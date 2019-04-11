<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BulkPackage extends BaseModel
{
    use SoftDeletes;

	protected $table = 'bulk_packages';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'classes_number', 'price', 'expiry_days', 'type', 'status'];
}
