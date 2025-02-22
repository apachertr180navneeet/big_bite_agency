<?php

namespace App\Imports;

    use App\Models\{Invoice,Customer,User};
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;
    use Carbon\Carbon;

class InvoiceImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $customerName = isset($row['customer']) ? str_replace(['[', ']'], ['(', ')'], $row['customer']) : null;
        // Fetch customer ID from the customers table
        $customer = Customer::where('firm', $row['customer'])->first();
        $customerId = $customer ? $customer->id : null;

        // Fetch assign (user) ID from the users table
        $assignUser = User::where('full_name', $row['assign'])->first();
        $assignId = $assignUser ? $assignUser->id : null;

        $formattedDate = null;
        if (!empty($row['date'])) {
            try {
                $formattedDate = Carbon::parse($row['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Handle parsing errors if necessary
                $formattedDate = null;
            }
        }
        return Invoice::firstOrCreate(
            ['invoice' => $row['invoice']],
            [
                'customer' => $customerId, // Store customer ID
                'assign' => $assignId, // Store user ID
                'amount' => $row['amount'] ?? null,
                'date' => $formattedDate,
            ]
        );
    }
}
