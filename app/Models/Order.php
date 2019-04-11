<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{
    use SoftDeletes;

	protected $table = 'orders';
    protected $dates = ['deleted_at'];
    protected $fillable = ['order_number', 'full_name', 'mobile', 'country', 'email', 'title', 'price', 'foreign_price', 'foreign_currency', 'class_id', 'bulk_package_id', 'bulk_qty', 'bulk_type', 'bulk_expires_at', 'bulk_instructor_id', 'user_id', 'status_id', 'gateway_response', 'status'];

    // Relationships
    public function member()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'status_id');
    }

    public function instructor()
    {
        return $this->belongsTo('App\Models\User', 'bulk_instructor_id');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking', 'order_id');
    }

    // Attributes
    public function getTotalBulkUsedAttribute($value)
    {
        return $this->bookings()->where('bookings.status', 1)->count();
    }

    // Scopes
    public function scopePaidOrder($query)
    {
        return $query->where('status', 1)->where('status_id', 2);
    }
}
