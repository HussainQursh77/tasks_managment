<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
        $rules = [
            'firstname' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'lastname' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $this->route('user')->id,
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:14',
                'confirmed',
            ],
        ];

        // Allow admins to update the role field
        if (auth()->user()->role === 'admin') {
            $rules['role'] = ['in:admin,user', 'nullable'];
        }

        return $rules;
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
        if ($this->has('firstname') && $this->has('lastname')) {
            $this->merge([
                'name' => $this->firstname . ' ' . $this->lastname,
            ]);
        }
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($this->name)) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
