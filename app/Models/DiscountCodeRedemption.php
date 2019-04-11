<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCodeRedemption extends BaseModel
{
    use SoftDeletes;

	protected $table = 'discount_codes_redemptions';
    protected $dates = ['deleted_at'];
    protected $fillable = ['discount_id', 'user_id', 'status'];

    // Relationships
    public function member()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function discountCode()
    {
        return $this->belongsTo('App\Models\DiscountCode', 'discount_id');
    }
}
