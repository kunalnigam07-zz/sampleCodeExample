<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClassEvent extends BaseModel
{
    use SoftDeletes;

	protected $table = 'classes';
    protected $dates = ['deleted_at'];
    protected $fillable = ['title', 'price', 'class_at', 'class_ends_at', 'about', 'parent_id', 'max_number', 'level', 'privacy', 'video_url', 'img', 'flat_colour', 'type_1_id', 'type_2_id', 'type_3_id', 'bulk_allowed', 'tb_session', 'tb_token', 'actual_start_at', 'actual_end_at', 'cancelled_at', 'user_id', 'published', 'status', 'has_music', 'broadcast_url', 'broadcast', 'broadcast_guid', 'tokbox_api_choice', 'tokbox_js_instructor', 'tokbox_js_member', 'record_class', 'liveswitch_appid', 'actual_ready_at'];

    // Relationships
    public function equipment()
    {
        return $this->hasMany('App\Models\ClassEquipment', 'class_id');
    }

    public function classInstructor()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function classType1()
    {
        return $this->belongsTo('App\Models\ClassType', 'type_1_id');
    }

    public function classType2()
    {
        return $this->belongsTo('App\Models\ClassType', 'type_2_id');
    }

    public function classType3()
    {
        return $this->belongsTo('App\Models\ClassType', 'type_3_id');
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\Booking', 'class_id');
    }

    // Attributes
    public function getTotalBookedAttribute() // Used in CMS
    {
        return $this->bookings()->count();
    }

    // Attributes
    // Commented out, currently using ClassHelper::classStatus() instead for front end
    /*public function getTotalBookedAttribute()
    {
        return $this->bookings()->count();
    }*/
}
