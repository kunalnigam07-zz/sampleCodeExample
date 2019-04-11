<?php

namespace App\Models;

use Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'users';
    protected $fillable = ['name', 'surname', 'dob', 'mobile_country_id', 'mobile', 'img', 'bio', 'accreditations', 'years_experience', 'signature_img', 'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube', 'social_linkedin', 'social_googleplus', 'social_snapchat', 'social_www', 'country_id', 'bank_name', 'bank_account_holder', 'bank_account_number', 'bank_sort_code', 'paypal_email', 'newsfeed_last_at', 'notifications_last_at', 'dismissed_message_ids', 'dismissed_feed_ids', 'rating', 'ratings', 'members_trained', 'classes_held', 'class_types', 'stats_at', 'has_insurance', 'has_accreditations', 'email', 'password', 'timezone', 'login_at', 'ip', 'user_type', 'admin_type', 'pre_authenticated', 'braintree_id', 'status', 'city'];
    protected $hidden = ['password', 'remember_token'];

    // Relationships
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission');
    }

    public function interests()
    {
        return $this->belongsToMany('App\Models\ClassType', 'interests', 'user_id', 'class_type_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function mobileCountry()
    {
        return $this->belongsTo('App\Models\Country', 'mobile_country_id');
    }

    public function classes()
    {
        return $this->hasMany('App\Models\ClassEvent', 'user_id');
    }

    // Attributes
    public function getTotalPublicClassesAttribute()
    {
        return $this->classes()->where('status', 1)->where('privacy', 1)->where('published', 1)->where('classes.class_at', '>=', Carbon::now())->count();
    }

    // Scopes
    public function scopeMembers($query)
    {
        return $query->where('user_type', 3);
    }

    public function scopeInstructors($query)
    {
        return $query->where('user_type', 2);
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', 1);
    }

    public function scopeMembersAndInstructors($query)
    {
        return $query->whereIn('user_type', [2, 3]);
    }

    // Events
    public static function boot()
	{
		parent::boot();

		static::deleting(function ($model) {
			$model->email = 'deleted_' . time() . '_' . $model->email;
            $model->status = 0;
			$model->save();
            $model->permissions()->detach();
		});
	}
}
