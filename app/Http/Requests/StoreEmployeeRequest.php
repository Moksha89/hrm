<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|digits:10|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'team_id' => 'nullable|exists:teams,id',
            'salary' => 'nullable|numeric|min:0',
            'loan' => 'nullable|numeric|min:0',
            'emi' => 'nullable|numeric|min:0',
            'aadhar' => 'nullable|digits:12',
            'pan' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'dob' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'bank_accounts' => 'required|array|min:1',
            'bank_accounts.*.account_holder_name' => 'required|string|max:255',
            'bank_accounts.*.account_number' => 'required|string|max:255',
            'bank_accounts.*.ifsc_code' => 'required|string|size:11',
            'bank_accounts.*.bank_name' => 'required|string|max:255',
            'bank_accounts.*.is_default' => 'boolean',
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'documents.*.document_name' => 'required|string|max:255',
            'documents.*.expiry_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be 10 digits.',
            'mobile.unique' => 'This mobile number is already registered.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'team_id.exists' => 'Selected team does not exist.',
            'aadhar.digits' => 'Aadhar number must be 12 digits.',
            'pan.regex' => 'PAN number format is invalid.',
            'bank_accounts.required' => 'At least one bank account is required.',
            'bank_accounts.*.account_holder_name.required' => 'Account holder name is required.',
            'bank_accounts.*.account_number.required' => 'Account number is required.',
            'bank_accounts.*.ifsc_code.required' => 'IFSC code is required.',
            'bank_accounts.*.ifsc_code.size' => 'IFSC code must be 11 characters.',
            'bank_accounts.*.bank_name.required' => 'Bank name is required.',
            'documents.*.file.required' => 'Document file is required.',
            'documents.*.file.mimes' => 'Document must be a PDF, DOC, DOCX, JPG, JPEG, or PNG file.',
            'documents.*.file.max' => 'Document size must not exceed 10MB.',
            'documents.*.document_name.required' => 'Document name is required.',
        ];
    }
}
