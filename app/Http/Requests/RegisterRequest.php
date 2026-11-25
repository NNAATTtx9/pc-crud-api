<?php

namespace App\Http\Requests;

use App\Models\RunCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minDonation = RunCategory::find((int) $this->input('category_id'))?->price ?? 1;

        return [
            'category_id'     => ['required', 'integer', Rule::exists('run_category', 'id')->where('is_active', 1)],
            'name'            => 'required|string|min:2|max:100',
            'email'           => 'required|email|max:191',
            'mobile'          => 'required|string|min:8|max:20',
            'tshirt_size'     => 'required|in:XS,S,M,L,XL,XXL',
            'organisation'    => 'nullable|string|max:191',
            'donation_amount' => "required|integer|min:$minDonation",
            'pdpa_consent'    => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'pdpa_consent.accepted' => 'You must agree to the PDPA consent to register',
        ];
    }
}

