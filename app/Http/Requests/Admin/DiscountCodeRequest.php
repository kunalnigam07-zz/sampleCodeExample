<?php

namespace App\Http\Requests\Admin;

class DiscountCodeRequest extends AdminRequest
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
        if ($this->is('admin/orders/discount-codes/edit/*')) {
            return [
                'title' => 'required',
                'code' => 'required|unique:discount_codes,code,' . $this->segment(5),
                'starts_at' => 'required',
                'ends_at' => 'required',
                'class_max_number' => 'required'
            ];
        } else {
            return [
                'title' => 'required',
                'code' => 'required|unique:discount_codes',
                'starts_at' => 'required',
                'ends_at' => 'required',
                'class_max_number' => 'required',
                'num_codes' => 'required|integer|min:1'
            ];
        }
    }
}
