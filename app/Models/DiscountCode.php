<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends BaseModel
{
    use SoftDeletes;

	protected $table = 'discount_codes';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'code', 'type', 'email', 'starts_at', 'ends_at', 'instructor_id', 'user_id', 'cancelled_booking_id', 'notes', 'status', 'class_max_number'];

    // Relationships
    public function instructor() // Only applicable sometimes
    {
        return $this->belongsTo('App\Models\User', 'instructor_id');
    }

    public function booking() // Only applicable sometimes
    {
        return $this->belongsTo('App\Models\Booking', 'cancelled_booking_id');
    }

    public function redeem() // Only applicable sometimes
    {
        return $this->hasMany('App\Models\DiscountCodeRedemption', 'discount_id');
    }
}
