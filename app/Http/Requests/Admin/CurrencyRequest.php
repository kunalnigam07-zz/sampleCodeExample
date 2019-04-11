<?php

namespace App\Http\Requests\Admin;

class CurrencyRequest extends AdminRequest
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
            'code' => 'required',
            'symbol' => 'required',
            'merchant_id' => 'required',
            'rate' => 'required',
            'rate_min' => 'required',
            'profit_rate' => 'required',
            'ordering' => 'required|integer'
        ];
    }
}
