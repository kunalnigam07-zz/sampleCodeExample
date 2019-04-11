<?php

namespace App\Http\Requests\Admin;

class ClassEventRequest extends AdminRequest
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
            'title' => 'required',
            'type_array' => 'required',
            'user_id' => 'required|integer',
            'price' => 'required',
            'class_at' => 'required',
            'max_number' => 'required|integer|min:1'
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
            'type_array.required' => 'The type field is required.',
            'user_id.required' => 'The instructor field is required.',
            'max_number.required' => 'The maximum members field is required.'
        ];
    }
}
