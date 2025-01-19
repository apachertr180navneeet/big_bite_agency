<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Customer,
    Invoice
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function customerList(Request $request)
    {
        try {
            $id = auth()->user()->id;
            // Define pagination parameters
            $perPage = 10;

            // Get the current page from the request, defaulting to 1
            $page = $request->input('page', 1);

            // Paginate the customers with necessary joins
            $invoices = Invoice::where('invoices.assign', $id)
                ->join('users', 'invoices.assign', '=', 'users.id')
                ->join('customers', 'invoices.customer', '=', 'customers.id')
                ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id')
                ->select(
                    'invoices.id',
                    'customers.name as customers_name',
                    'customers.city as customers_city',
                    'customers.phone as customers_phone',
                    DB::raw('COALESCE(receipts.remaing_amount, invoices.amount) as due')
                )
                ->paginate($perPage);

            // Check if invoices are found
            if ($invoices->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            // Transform the invoices into a simple array for the response
            $invoiceData = $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'customers_name' => $invoice->customers_name,
                    'customers_city' => $invoice->customers_city,
                    'customers_phone' => $invoice->customers_phone,
                    'due' => $invoice->due, // This will contain either remaining_amount or invoice amount
                ];
            });

            // Handle pagination data, replace null with empty string
            $pagination = [
                'current_page' => $invoices->currentPage() ?? "",
                'total_pages' => $invoices->lastPage() ?? "",
                'total_items' => $invoices->total() ?? "",
                'items_per_page' => $invoices->perPage() ?? "",
                'current_url' => $invoices->url($invoices->currentPage()) ?? "",
                'last_url' => $invoices->url($invoices->lastPage()) ?? "",
                'previous_url' => $invoices->previousPageUrl() ?? "",
                'next_url' => $invoices->nextPageUrl() ?? "",
                'next_page' => $invoices->hasMorePages() ? $invoices->currentPage() + 1 : "",
            ];

            // Return the response with updated pagination details
            return response()->json([
                'status' => true,
                'message' => 'Customers found successfully.',
                'customer' => $invoiceData,
                'pagination' => $pagination,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500); // Return a 500 status code on error
        }
    }
}
