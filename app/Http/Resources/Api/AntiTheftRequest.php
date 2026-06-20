<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\AntiTheftPhone;

class AntiTheftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For API requests, authorization is handled by Sanctum middleware
        // Additional business logic authorization can be added here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        switch ($this->getMethod()) {
            case 'POST':
                $rules = $this->getCreateRules();
                break;
            case 'PUT':
            case 'PATCH':
                $rules = $this->getUpdateRules();
                break;
        }

        return $rules;
    }

    /**
     * Get validation rules for creating a new phone record.
     */
    protected function getCreateRules(): array
    {
        return [
            'serial_number' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\-_]+$/',
                'unique:anti_theft_phones,serial_number'
            ],
            'phone_model' => 'required|string|max:255',
            'phone_brand' => 'required|string|max:255',
            'phone_color' => 'nullable|string|max:100',
            'status' => 'nullable|in:' . implode(',', array_keys(AntiTheftPhone::getStatuses())),
            'external_api_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get validation rules for updating an existing phone record.
     */
    protected function getUpdateRules(): array
    {
        $serialNumber = $this->route('serial_number');
        
        return [
            'serial_number' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\-_]+$/',
                'unique:anti_theft_phones,serial_number,' . $serialNumber . ',serial_number'
            ],
            'phone_model' => 'sometimes|string|max:255',
            'phone_brand' => 'sometimes|string|max:255',
            'phone_color' => 'nullable|string|max:100',
            'status' => 'sometimes|in:' . implode(',', array_keys(AntiTheftPhone::getStatuses())),
            'status_reason' => 'nullable|string|max:500',
            'external_api_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'serial_number.required' => 'The serial number is required.',
            'serial_number.unique' => 'A phone with this serial number already exists.',
            'serial_number.regex' => 'The serial number format is invalid. Only alphanumeric characters, hyphens, and underscores are allowed.',
            'phone_model.required' => 'The phone model is required.',
            'phone_brand.required' => 'The phone brand is required.',
            'status.in' => 'The selected status is invalid.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'external_api_id.max' => 'External API ID cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'serial_number' => 'serial number',
            'phone_model' => 'phone model',
            'phone_brand' => 'phone brand',
            'phone_color' => 'phone color',
            'external_api_id' => 'external API ID',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
                'error_count' => count($errors),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and normalize the serial number
        if ($this->has('serial_number')) {
            $this->merge([
                'serial_number' => strtoupper(trim($this->serial_number))
            ]);
        }

        // Normalize phone brand and model
        if ($this->has('phone_brand')) {
            $this->merge([
                'phone_brand' => ucwords(strtolower(trim($this->phone_brand)))
            ]);
        }

        if ($this->has('phone_model')) {
            $this->merge([
                'phone_model' => trim($this->phone_model)
            ]);
        }

        if ($this->has('phone_color')) {
            $this->merge([
                'phone_color' => ucfirst(strtolower(trim($this->phone_color)))
            ]);
        }

        // Set default status if not provided
        if ($this->isMethod('POST') && !$this->has('status')) {
            $this->merge([
                'status' => AntiTheftPhone::STATUS_AWAITING_VERIFICATION
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Custom validation logic can be added here
            
            // Validate serial number format based on brand
            if ($this->has('serial_number') && $this->has('phone_brand')) {
                $this->validateSerialNumberFormat($validator);
            }

            // Validate status transitions
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $this->validateStatusTransition($validator);
            }
        });
    }

    /**
     * Validate serial number format based on phone brand.
     */
    protected function validateSerialNumberFormat(Validator $validator): void
    {
        $serialNumber = $this->serial_number;
        $brand = strtolower($this->phone_brand);

        // Add brand-specific serial number validation rules
        switch ($brand) {
            case 'apple':
            case 'iphone':
                if (!preg_match('/^[A-Z0-9]{10,15}$/', $serialNumber)) {
                    $validator->errors()->add('serial_number', 'Invalid Apple device serial number format.');
                }
                break;
            
            case 'samsung':
                if (!preg_match('/^[A-Z0-9]{11,17}$/', $serialNumber)) {
                    $validator->errors()->add('serial_number', 'Invalid Samsung device serial number format.');
                }
                break;
            
            default:
                // Generic validation for other brands
                if (strlen($serialNumber) < 6 || strlen($serialNumber) > 25) {
                    $validator->errors()->add('serial_number', 'Serial number must be between 6 and 25 characters.');
                }
                break;
        }
    }

    /**
     * Validate status transitions.
     */
    protected function validateStatusTransition(Validator $validator): void
    {
        if (!$this->has('status')) {
            return;
        }

        $serialNumber = $this->route('serial_number');
        $newStatus = $this->status;

        // Get current phone record
        $currentPhone = \App\Models\AntiTheftPhone::bySerialNumber($serialNumber)->first();
        
        if (!$currentPhone) {
            return; // Will be handled by the controller
        }

        $currentStatus = $currentPhone->status;

        // Define invalid status transitions
        $invalidTransitions = [
            AntiTheftPhone::STATUS_BLACKLISTED => [
                AntiTheftPhone::STATUS_IN_USE,
                AntiTheftPhone::STATUS_AWAITING_VERIFICATION
            ],
            AntiTheftPhone::STATUS_REPORTED_STOLEN => [
                AntiTheftPhone::STATUS_IN_USE,
                AntiTheftPhone::STATUS_AWAITING_VERIFICATION
            ]
        ];

        if (isset($invalidTransitions[$currentStatus]) && 
            in_array($newStatus, $invalidTransitions[$currentStatus])) {
            $validator->errors()->add('status', 
                "Cannot change status from '{$currentStatus}' to '{$newStatus}' without proper verification."
            );
        }

        // Require reason for certain status changes
        $reasonRequiredFor = [
            AntiTheftPhone::STATUS_REPORTED_STOLEN,
            AntiTheftPhone::STATUS_BLACKLISTED,
            AntiTheftPhone::STATUS_RECOVERED
        ];

        if (in_array($newStatus, $reasonRequiredFor) && !$this->has('status_reason')) {
            $validator->errors()->add('status_reason', 
                "A reason is required when changing status to '{$newStatus}'."
            );
        }
    }
}