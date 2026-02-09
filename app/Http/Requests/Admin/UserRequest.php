<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:225',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'address' => 'nullable|string|max:255',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode_id' => 'nullable|exists:pincodes,id',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
            'gender' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        if ($this->isMethod('put') && $this->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'email.unique' => 'The email has already been taken.',
            'mobile.unique' => 'The mobile number has already been taken.',
        ];
    }
}
