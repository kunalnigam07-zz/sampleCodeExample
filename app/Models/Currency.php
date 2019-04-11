<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends BaseModel
{
    use SoftDeletes;

	protected $table = 'currencies';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'code', 'symbol', 'merchant_id', 'rate', 'rate_min', 'profit_rate', 'ordering', 'status'];
}
