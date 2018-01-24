<?php

namespace App\Http\Requests\Debt;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('debt-create');
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
            'status' => 'required',
            'user_assigned_id' => 'required',
            'user_created_id' => '',
            'client_id' => 'required',
            'payment_schedule' => 'required'
        ];
    }
}
