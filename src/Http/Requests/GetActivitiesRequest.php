<?php

namespace Vanguard\UserActivity\Http\Requests;

use Vanguard\Http\Requests\Request;

class GetActivitiesRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'per_page' => 'integer|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.max' => __('Maximum number of records per page is 100.'),
        ];
    }
}
