<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThesisRequest extends FormRequest
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
    public function rules()
    {
        return [
            'student_id'         => 'required|string',
            'title'              => 'required|string|max:255',
            'start_date'         => 'required|date',
            'final_document_url' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ];
    }
}
