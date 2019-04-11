<?php

namespace App\Http\Requests\Admin;

class BulkPackageRequest extends AdminRequest
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
            'user_id' => 'required|integer',
            'classes_number' => 'required|integer|min:1',
            'price' => 'required',
            'expiry_days' => 'required|integer|min:1'
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
            'user_id.required' => 'The instructor field is required.',
            'classes_number.required' => 'The number of classes is required.',
            'expiry_days.required' => 'The expiry in days is required.'
        ];
    }
}
