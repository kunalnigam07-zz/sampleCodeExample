<?php

namespace App\Http\Requests\Admin;

class BookingRequest extends AdminRequest
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
            'class_id' => 'required|integer',
            'user_id' => 'required|integer'
        ];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'class_id.required' => 'The class field is required.',
            'user_id.required' => 'The user field is required.'
        ];
    }
}
