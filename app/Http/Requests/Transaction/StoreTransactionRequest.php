<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:App\Models\TransactionType,id',
            'spent_money' => 'required',
            'spent_date' => 'required|date_format:'.config('app.datetime_format'),
            'remarks' => 'nullable',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'spent_money' => 'value of spent money',
            'spent_date' => 'date of spent money',
        ];
    }

    // /**
    //  * Get the error messages for the defined validation rules.
    //  *
    //  * @return array<string, string>
    //  */
    // public function messages(): array
    // {
    //     return [
    //         'category_id.required' => 'A :attribute is required',
    //         'spent_money.required' => 'A :attribute is required',
    //         'spent_date.required' => 'A :attribute is required',
    //     ];
    // }

}
