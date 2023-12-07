<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class adminbulderRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'builder_email' => 'required|email',
            'brand_id' => 'required|exists:brands,id',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'abn' => 'required|string',
        ];
    }
}
