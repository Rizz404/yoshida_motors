<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppraisalRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_brand' => 'required|string|max:50',
            'vehicle_model' => 'required|string|max:50',
            'year_manufacture' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'photos' => 'required|array|min:1',
            'photos.*.image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'photos.*.category' => 'required|string',
        ];
    }
}
