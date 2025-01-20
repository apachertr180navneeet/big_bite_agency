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
            // Get the authenticated user's ID
            $id = auth()->user()->id;

            // Define pagination parameters (items per page)
            $perPage = 10;

            // Fetch invoices with the necessary joins and conditions
            // Filtering by the authenticated user's assigned invoices with 'pending' payment status
            $invoices = Invoice::where('invoices.assign', $id)
                ->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->join('users', 'invoices.assign', '=', 'users.id') // Join with 'users' table to fetch assigned user info
                ->join('customers', 'invoices.customer', '=', 'customers.id') // Join with 'customers' table to fetch customer info
                ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id') // Left join with 'receipts' to get payment details (if available)
                ->select(
                    'customers.id as customer_id', // Select customer ID
                    'customers.name as customers_name', // Select customer name
                    'customers.city as customers_city', // Select customer city
                    'customers.phone as customers_phone', // Select customer phone
                    DB::raw('COUNT(invoices.id) as pending_invoices_count'), // Count the number of pending invoices for each customer
                    DB::raw('SUM(COALESCE(receipts.remaing_amount, invoices.amount)) as due') // Calculate the due amount (sum of remaining amount or invoice amount)
                )
                ->groupBy(
                    'customers.id', // Group by customer ID
                    'customers.name', // Group by customer name
                    'customers.city', // Group by customer city
                    'customers.phone' // Group by customer phone
                )
                ->paginate($perPage); // Paginate the results (10 items per page)

            // If no invoices are found, return a 'Customer not found' response
            if ($invoices->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            // Map the invoice data to a simpler structure for the response
            // Each invoice contains customer details and the pending invoice count/due amount
            $invoiceData = $invoices->map(function ($invoice) {
                return [
                    'customer_id' => $invoice->customer_id, // Customer ID
                    'customers_name' => $invoice->customers_name, // Customer name
                    'customers_city' => $invoice->customers_city, // Customer city
                    'customers_phone' => $invoice->customers_phone, // Customer phone
                    'due' => $invoice->due, // Due amount
                    'total_bill' => $invoice->pending_invoices_count, // Total number of pending invoices
                ];
            });

            // Return the response with the customer data and pagination details
            return response()->json([
                'status' => true,
                'message' => 'Customers found successfully.',
                'customer' => $invoiceData, // Customer data (transformed)
                'pagination' => [
                    'current_page' => $invoices->currentPage(), // Current page number
                    'total_pages' => $invoices->lastPage(), // Total number of pages
                    'total_items' => $invoices->total(), // Total number of items (customers)
                    'items_per_page' => $invoices->perPage(), // Items per page
                    'current_url' => $invoices->url($invoices->currentPage()), // URL for the current page
                    'last_url' => $invoices->url($invoices->lastPage()), // URL for the last page
                    'previous_url' => $invoices->previousPageUrl(), // URL for the previous page (if exists)
                    'next_url' => $invoices->nextPageUrl(), // URL for the next page (if exists)
                    'next_page' => $invoices->hasMorePages() ? $invoices->currentPage() + 1 : "", // Next page number (if exists)
                ],
            ], 200);

        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the exception message
            ], 500); // Return a 500 internal server error status
        }
    }
}
