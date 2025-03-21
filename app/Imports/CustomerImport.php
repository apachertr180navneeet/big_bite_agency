<?php

    namespace App\Imports;

    use App\Models\Customer;
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;

    class CustomerImport implements ToModel, WithHeadingRow
    {
        public function model(array $row)
        {
            // Replace square brackets with parentheses in the firm name
            $firmName = isset($row['fim_name']) ? str_replace(['[', ']'], ['(', ')'], $row['fim_name']) : null;

            // Check if a customer with the same firm name already exists
            $existingCustomer = Customer::where('firm', $firmName)->exists();

            if ($existingCustomer) {
                // If the firm name already exists, return null or handle it accordingly
                return null; // You can log this or throw an exception if needed
            }

            // Create a new customer if firm name is unique
            return Customer::create([
                'firm'    => $row['fim_name']?? null,
                'name'    => $row['name'] ?? null,
                'phone'   => $row['phone'] ?? null,
                'gst_no'  => $row['gst_no'] ?? null,
                'address1' => $row['address1'] ?? null,
                'city'    => $row['city'] ?? null,
                'state'   => $row['state'] ?? null,
            ]);
        }

    }