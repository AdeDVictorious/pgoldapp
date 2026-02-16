<?php

namespace App\Http\Requests;

use App\Traits\HttpResponses; 
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    use HttpResponses; 

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'lastname'  => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email'     => 'required|email|unique:users,email',
            'password'  => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/'
            ]
        ];
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

    protected function prepareForValidation()
    {
        $this->merge([
            'firstname' => trim(strtolower(preg_replace('/\s+/', ' ', $this->firstname))),
            'lastname'  => trim(strtolower(preg_replace('/\s+/', ' ', $this->lastname))),
            'email'     => trim(strtolower(preg_replace('/\s+/', ' ', $this->email))),
        ]);
    }

    public function messages()
    {
        return [
            'firstname.regex' => 'only letters is allowed',
            'lastname.regex'  => 'only letters is allowed',
            'password.regex'  => 'password must contain at least one uppercase, lowercase, number and special character',
        ];
    }
}