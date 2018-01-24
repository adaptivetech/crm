<?php

namespace App\Http\Requests\Debt;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('debt-update');
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
            'user_created_id' => '',
            'payment_schedule' => 'required'
        ];
    }
}
