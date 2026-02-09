<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = auth()->id();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:225',
                'unique:users,email,' . $userId,
            ],
            'mobile' => [
                'required',
                'string',
                'max:15',
                'unique:users,mobile,' . $userId,
            ],
            'address' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'city_id' => 'required|exists:cities,id',
            'pincode_id' => 'required|exists:pincodes,id',
            'gender' => 'required|in:Male,Female,Other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card' => 'nullable|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|max:10',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/|max:11',
        ];

        // Password validation: only if password field is filled
        if ($this->filled('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already taken.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.unique' => 'This mobile number is already taken.',
            'address.required' => 'Address is required.',
            'state_id.required' => 'Please select a state.',
            'district_id.required' => 'Please select a district.',
            'city_id.required' => 'Please select a city.',
            'pincode_id.required' => 'Please select a pincode.',
            'gender.required' => 'Please select your gender.',
            'current_password.required' => 'Current password is required to change password.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image must not be larger than 2MB.',
            'pan_card.regex' => 'Invalid PAN card format. Format: ABCDE1234F',
            'ifsc_code.regex' => 'Invalid IFSC code format.',
        ];
    }
}
