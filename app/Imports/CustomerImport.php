<?php

    namespace App\Imports;

    use App\Models\Customer;
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;

    class CustomerImport implements ToModel, WithHeadingRow
    {
        public function model(array $row)
        {
            return Customer::firstOrCreate(
                ['firm' => $row['fim_name']],
                [
                    'name'     => $row['name'] ?? null,
                    'phone'    => $row['phone'] ?? null,
                    'gst_no'   => $row['gst_no'] ?? null,
                    'address1'  => $row['address1'] ?? null,
                    'city'     => $row['city'] ?? null,
                    'state'    => $row['state'] ?? null,
                ]
            );
        }
    }