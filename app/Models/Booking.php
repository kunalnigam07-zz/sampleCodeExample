<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends BaseModel
{
    use SoftDeletes;

	protected $table = 'bookings';
    protected $dates = ['deleted_at'];
    protected $fillable = ['class_id', 'user_id', 'rating', 'comments', 'order_id', 'discount_code_id', 'joined_at', 'refunded_at', 'notes', 'tb_token', 'status'];

    // Relationships
    public function classEvent()
    {
        return $this->belongsTo('App\Models\ClassEvent', 'class_id');
    }

    public function classUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
