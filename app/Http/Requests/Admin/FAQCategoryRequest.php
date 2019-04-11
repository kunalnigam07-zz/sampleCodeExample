<?php

namespace App\Http\Requests\Admin;

class FAQCategoryRequest extends AdminRequest
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
            'section_id' => 'required|integer',
            'ordering' => 'required|integer'
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
            'section_id.required' => 'The section field is required.'
        ];
    }
}
