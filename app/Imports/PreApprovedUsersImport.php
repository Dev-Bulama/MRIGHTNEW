<?php

namespace App\Imports;

use App\Models\PreApprovedUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PreApprovedUsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new PreApprovedUser([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'],
            'secondary_phone' => $row['secondary_phone'] ?? null,
            'user_type' => $row['user_type'] ?? 'shop_owner',
            'shop_name' => $row['shop_name'] ?? null,
            'business_address' => $row['business_address'] ?? null,
            'business_phone' => $row['business_phone'] ?? null,
            'state' => $row['state'] ?? null,
            'local_government' => $row['local_government'] ?? null,
            'status' => 'pending',
            'import_metadata' => [
                'imported_at' => now(),
                'imported_by' => auth()->id(),
            ]
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:pre_approved_users,email',
            'phone_number' => 'required|string|unique:pre_approved_users,phone_number',
        ];
    }
}