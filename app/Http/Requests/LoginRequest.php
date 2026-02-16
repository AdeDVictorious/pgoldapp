<?php

namespace App\Http\Requests;

use App\Traits\HttpResponses; 
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    use HttpResponses; 
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'email'    => 'required|email',
           'password'=>  [
                'required',
                'string',
                'min:8',            // Minimum length
                'regex:/[a-z]/',    // Must contain at least one lowercase letter
                'regex:/[A-Z]/',    // Must contain at least one uppercase letter
                'regex:/[0-9]/',    // Must contain at least one digit
                'regex:/[@$!%*?&#]/' // Must contain a special character
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

    /**
     * Prepare the data for validation. removing unnecessary spacing and triming
     */
    protected function prepareForValidation()
    {
        $emails = preg_replace('/\s+/', ' ', $this->email);        
        $email = trim(strtolower($emails));         

        $this->merge([
            'email' => $email,
        ]);
    }

    /**
     * return an error message from program_type input back to users
     */
    public function messages()
    {
        return [
            'password.regex' => 'password must contain at least one uppercase, lowercase, number and special charater',
        ];
    }

}
