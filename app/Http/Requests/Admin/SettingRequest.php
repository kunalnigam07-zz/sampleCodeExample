<?php

namespace App\Http\Requests\Admin;

class SettingRequest extends AdminRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'site_name' => 'required',
            'twilio_number' => 'required',
            'tokbox_key' => 'required',
            'tokbox_secret' => 'required',
            'email_feedback' => 'required',
            'email_notifications_name' => 'required',
            'email_notifications' => 'required'
        ];
    }
}
