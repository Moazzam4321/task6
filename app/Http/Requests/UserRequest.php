<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            'name' => 'required||regex:/^[\pL\s]+$/u|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|regex:/^.*([a-zA-Z])(?=.*[0-9]).*/',
            'address' => 'required|regex:/(^[-0-9A-Za-z.,\/ ]+$)/',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            
        ];
    }
}
