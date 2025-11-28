<?php

namespace App\Http\Requests;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'name' => 'required|string',
            'email' => 'required|email',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id'
        ];
    }
}
