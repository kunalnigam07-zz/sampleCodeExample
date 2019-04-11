<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends BaseModel
{
    use SoftDeletes;

	protected $table = 'orders_statuses';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'status'];
}
