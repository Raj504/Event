<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanquatHallRequest extends FormRequest
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
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,gif|max:5000',
            'featured_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:3500',
            'name' => 'required|max:100',
            'status' => 'required|max:10',
            'is_parking' => 'required|max:10',
            'description' => 'required|max:1000',
        ];
    }
}
