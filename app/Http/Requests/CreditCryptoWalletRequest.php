<?php

namespace App\Http\Requests;


use App\Traits\HttpResponses; 
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CreditCryptoWalletRequest extends FormRequest
{
    use HttpResponses; 

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true to allow the request to proceed
        return true; 
    }


        /**
     * Handle a failed validation attempt. 
     */
    protected function failedValidation(Validator $validator)
    {
        // This throws an exception that Laravel catches to return your JSON
        throw new HttpResponseException(
            $this->error(
                $validator->errors(), 
                'Validation errors occurred', 
                422
            )
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount'    => ['required', 'numeric'],
            'currency'  => ['required', 'string', 'max:4'], // e.g., BTC, USDT
            // 'is_active' => ['boolean'],
        ];
    }
}
