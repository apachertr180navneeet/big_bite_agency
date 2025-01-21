<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Customer,
    Invoice,
    Receipt
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
                    'current_page' => $invoices->currentPage() ?: "", 
                    'total_pages' => $invoices->lastPage() ?: "", 
                    'total_items' => $invoices->total() ?: "", 
                    'items_per_page' => $invoices->perPage() ?: "", 
                    'current_url' => $invoices->url($invoices->currentPage()) ?: "", 
                    'last_url' => $invoices->url($invoices->lastPage()) ?: "", 
                    'previous_url' => $invoices->previousPageUrl() ?: "", 
                    'next_url' => $invoices->nextPageUrl() ?: "", 
                    'next_page' => $invoices->hasMorePages() ? $invoices->currentPage() + 1 : "",
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

    public function customerReceipt(Request $request)
    {
        try {
            // Get the authenticated user's ID
            $id = auth()->user()->id;

            // Filtering by the authenticated user's assigned invoices with 'pending' payment status
            $invoices = Invoice::where('invoices.assign', $id)
                ->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->where('invoices.customer', $request->customer)
                ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id') // Left join with 'receipts' to get payment details (if available)
                ->select(
                    DB::raw('SUM(COALESCE(receipts.remaing_amount, invoices.amount)) as due') // Calculate the due amount (sum of remaining amount or invoice amount)
                )
                ->first(); // Get the first result (since we're expecting a single result)

                $invoicesLists = Invoice::where('invoices.assign', $id)
                ->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->where('invoices.customer', $request->customer)
                ->get(); // Get all matching invoices

                $invoiceBillNumber = [];  // Initialize the array to store invoice details

                foreach ($invoicesLists as $key => $value) {
                    $invoiceBillNumber[] = [
                        'invoice_id' => $value->id,
                        'invoice' => $value->invoice,  // Store the bill number
                    ];
                }

            // If no invoices are found, return a 'Customer not found' response
            if (!$invoices) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            // Return the response with the customer data
            return response()->json([
                'status' => true,
                'message' => 'Customer found successfully.',
                'remaining_amount' => $invoices->due, // Return the due amount
                'invoic_list' => $invoiceBillNumber
            ], 200);

        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the exception message
            ], 500); // Return a 500 internal server error status
        }
    }

    public function customerInvoiceDetail(Request $request)
    {
        try {
            // Get the authenticated user's ID
            $id = auth()->user()->id;

            $invoice = Invoice::where('invoices.id', $request->invoice)
                ->join('users', 'invoices.assign', '=', 'users.id')
                ->join('customers', 'invoices.customer', '=', 'customers.id')
                ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id')
                ->select('invoices.*', 'invoices.invoice as bill_number', 'invoices.customer as customers_id', 'invoices.assign as assign_id', 'customers.name as customers_name' , 'customers.discount as customers_discount' , 'users.full_name as assign_name')
                ->first();

                $discountAmount = $invoice->amount * ($invoice->customers_discount / 100);
                
                $invoice["max_discount_amount"] = $discountAmount;
            // If no invoices are found, return a 'Customer not found' response
            if (!$invoice) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            // Return the response with the customer data
            return response()->json([
                'status' => true,
                'message' => 'Invoice found successfully.',
                'invoic_detail' => $invoice
            ], 200);

        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the exception message
            ], 500); // Return a 500 internal server error status
        }
    }

    public function customerReceptStore(Request $request)
    {
        try {
            // Get the authenticated user's ID
            $id = auth()->user()->id;

            // Validation rules
            $rules = [
                'date' => 'required|string',
                'receipt' => 'required|unique:receipts,receipt',
                'bill_id' => 'required',
                'amount' => 'required',
                'discount' => 'required',
                'full_payment' => 'required',
                'remark' => 'required',
                'mode' => 'required',
            ];

            // Validate the request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ]);
            }

            $user = Auth::user();

            $compId = $user->firm_id;
            // Save the User data
            $dataUser = [
                'date' => $request->date,
                'receipt' => $request->receipt,
                'bill_id' => $request->bill_id,
                'assign' => $id,
                'amount' => $request->amount,
                'discount' => $request->discount,
                'full_payment' => $request->full_payment,
                'remark' => $request->remark,
                'mode' => $request->mode,
                'remaing_amount' => '0',
            ];
            $receipt = Receipt::create($dataUser);

            // Return the response with the customer data
            return response()->json([
                'status' => true,
                'message' => 'Recept Created Succesfully.',
                'invoic_detail' => $receipt
            ], 200);

        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the exception message
            ], 500); // Return a 500 internal server error status
        }
    }

    public function customerLeger($legerid)
    {
        try {
            // Get the authenticated user's ID
            
            echo $legerid; die;

            // Return the response with the customer data
            return response()->json([
                'status' => true,
                'message' => 'Recept Created Succesfully.',
                'invoic_detail' => $receipt
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
