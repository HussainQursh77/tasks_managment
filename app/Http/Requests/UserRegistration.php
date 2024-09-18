<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegistration extends FormRequest
{
    protected $stopOnFirstFailuer = true;
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'firstname' => strtolower($this->firstname),
            'lastname' => strtolower($this->lastname),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'lastname' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:14',
                'confirmed'
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $this->merge([
            'name' => $this->firstname . ' ' . $this->lastname,
        ]);
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['name'] = $this->name;
        return $data;
    }
}
