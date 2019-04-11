<?php

namespace App\Http\Requests\Admin;

class UserRequest extends AdminRequest
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
        if ($this->is('admin/settings/users/edit/*')) {
            return [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $this->segment(5)
            ];
        } else {
            return [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ];
        }
    }
}
