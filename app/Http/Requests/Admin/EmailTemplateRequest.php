<?php

namespace App\Http\Requests\Admin;

class EmailTemplateRequest extends AdminRequest
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
            'html_version' => 'required',
            'text_version' => 'required',
            'from_name' => 'required',
            'from_email' => 'required|email',
            'subject' => 'required'
        ];
    }
}
