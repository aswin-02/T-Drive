<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'name.required' => 'The role name is required.',
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'Please select at least one permission.',
        ];
    }

    public function rules()
    {
        $role = $this->route('role');
        $id = $role ? $role : null;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($id),
            ],
            'permissions' => 'required|array',
        ];
    }
}
