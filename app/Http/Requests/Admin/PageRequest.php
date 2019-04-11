<?php

namespace App\Http\Requests\Admin;

class PageRequest extends AdminRequest
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
        if ($this->is('admin/content/pages/edit/*')) {
            return [
                'title' => 'required',
                'ordering' => 'required|integer',
                'url' => 'required|unique:pages,url,' . $this->segment(5)
            ];
        } else {
            return [
                'title' => 'required',
                'ordering' => 'required|integer',
                'url' => 'required|unique:pages'
            ];
        }
    }
}
