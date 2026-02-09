<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class SignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|numeric|digits_between:10,15|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'referral_code' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a valid text',
            'name.max' => 'Name cannot exceed 255 characters',
            
            'mobile.required' => 'Mobile number is required',
            'mobile.numeric' => 'Mobile number must contain only numbers',
            'mobile.digits_between' => 'Mobile number must be between 10 to 15 digits',
            'mobile.unique' => 'Mobile number already registered',
            
            'password.required' => 'Password is required',
            'password.string' => 'Password must be valid',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            
            'password_confirmation.required' => 'Please confirm your password',
            
            'referral_code.string' => 'Referral code must be valid',
            'referral_code.max' => 'Referral code cannot exceed 255 characters',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate referral code exists if provided
            if ($this->filled('referral_code')) {
                $referrer = User::where('invite_code', $this->referral_code)
                    ->where('user_type', 'user')
                    ->first();
                
                if (!$referrer) {
                    $validator->errors()->add('referral_code', 'Enter a valid Referral Code');
                }
            }
        });
    }
}
