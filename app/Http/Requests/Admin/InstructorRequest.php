<?php

namespace App\Http\Requests\Admin;

class InstructorRequest extends AdminRequest
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
        if ($this->is('admin/instructors/instructors/edit/*')) {
            return [
                'name' => 'required',
                'surname' => 'required',
                'timezone' => 'required',
                'mobile_country_id' => 'required|integer',
                'country_id' => 'required|integer',
                'years_experience' => 'required|integer',
                'email' => 'required|email|unique:users,email,' . $this->segment(5) . ',id,user_type,2'
            ];
        } else {
            return [
                'name' => 'required',
                'surname' => 'required',
                'timezone' => 'required',
                'mobile_country_id' => 'required|integer',
                'country_id' => 'required|integer',
                'years_experience' => 'required|integer',
                'email' => 'required|email|unique:users,email,NULL,id,user_type,2'
            ];
        }
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The first name field is required.',
            'surname.required' => 'The last name field is required.',
            'country_id.required' => 'The country field is required.'
        ];
    }
}
