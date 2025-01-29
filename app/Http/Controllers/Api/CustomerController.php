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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
            $invoices = Invoice::where('invoices.assign', $id)
                //->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->join('users', 'invoices.assign', '=', 'users.id') // Join with 'users' table
                ->join('customers', 'invoices.customer', '=', 'customers.id') // Join with 'customers' table
                ->select(
                    'customers.id as customer_id',
                    'customers.firm as customers_name',
                    'customers.city as customers_city',
                    'customers.phone as customers_phone',
                    DB::raw('COUNT(invoices.id) as total_invoices'), // Total invoices count
                    DB::raw('SUM(invoices.amount) as total_invoice_amount') // Total invoice amount
                )
                ->groupBy('customers.id', 'customers.name', 'customers.city', 'customers.phone') // Group by customer
                ->paginate($perPage);
                
            // Calculate the due amount for each customer
            $invoiceData = $invoices->map(function ($invoice) {
                // Fetch total receipts for this customer
                $receiptData = Receipt::whereHas('invoice', function ($query) use ($invoice) {
                    $query->where('customer', $invoice->customer_id);
                })->selectRaw('SUM(amount + IFNULL(discount, 0)) as total_receipts')
                ->first();

                $totalReceipts = $receiptData->total_receipts ?? 0;

                return [
                    'customer_id' => $invoice->customer_id,
                    'customers_name' => $invoice->customers_name,
                    'customers_city' => $invoice->customers_city,
                    'customers_phone' => $invoice->customers_phone,
                    'total_bill' => $invoice->total_invoices,
                    'due' => $invoice->total_invoice_amount - $totalReceipts,
                ];
            });

            // Return the response with the customer data and pagination details
            return response()->json([
                'status' => true,
                'message' => 'Customers found successfully.',
                'customer' => $invoiceData,
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'total_pages' => $invoices->lastPage(),
                    'total_items' => $invoices->total(),
                    'items_per_page' => $invoices->perPage(),
                    'current_url' => $invoices->url($invoices->currentPage()),
                    'last_url' => $invoices->url($invoices->lastPage()),
                    'previous_url' => $invoices->previousPageUrl(),
                    'next_url' => $invoices->nextPageUrl(),
                    'next_page' => $invoices->hasMorePages() ? $invoices->currentPage() + 1 : null,
                ],
            ], 200);

        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function customerSearch(Request $request)
    {
        try {
            // Get the authenticated user's ID
            $id = auth()->user()->id;

            // Define pagination parameters (items per page)
            $perPage = 10;

            // Initialize the query for invoices
            $query = Invoice::where('invoices.assign', $id)
                //->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->join('users', 'invoices.assign', '=', 'users.id') // Join with 'users' table
                ->join('customers', 'invoices.customer', '=', 'customers.id') // Join with 'customers' table
                ->select(
                    'customers.id as customer_id',
                    'customers.firm as customers_name',
                    'customers.city as customers_city',
                    'customers.phone as customers_phone',
                    DB::raw('COUNT(invoices.id) as total_invoices'), // Total invoices count
                    DB::raw('SUM(invoices.amount) as total_invoice_amount') // Total invoice amount
                )
                ->groupBy('customers.id', 'customers.name', 'customers.city', 'customers.phone'); // Group by customer

            // Apply search filters for name, phone, and city if provided
                $query->Where('customers.name', 'like', '%' . $request->name . '%');
                $query->orWhere('customers.phone', 'like', '%' . $request->name . '%');
                $query->orWhere('customers.city', 'like', '%' . $request->name . '%');
            

            // Paginate the results
            $invoices = $query->paginate($perPage);

            // If no invoices are found, return a 'Customer not found' response
            if ($invoices->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            // Map the invoice data to a simpler structure for the response
            $invoiceData = $invoices->map(function ($invoice) {
                // Fetch total receipts for this customer
                $receiptData = Receipt::whereHas('invoice', function ($query) use ($invoice) {
                    $query->where('customer', $invoice->customer_id);
                })->selectRaw('SUM(amount + IFNULL(discount, 0)) as total_receipts')
                ->first();

                $totalReceipts = $receiptData->total_receipts ?? 0;

                return [
                    'customer_id' => $invoice->customer_id,
                    'customers_name' => $invoice->customers_name,
                    'customers_city' => $invoice->customers_city,
                    'customers_phone' => $invoice->customers_phone,
                    'total_bill' => $invoice->total_invoices,
                    'due' => $invoice->total_invoice_amount - $totalReceipts,
                ];
            });

            // Return the response with the customer data and pagination details
            return response()->json([
                'status' => true,
                'message' => 'Customers found successfully.',
                'customer' => $invoiceData, // Customer data (transformed)
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'total_pages' => $invoices->lastPage(),
                    'total_items' => $invoices->total(),
                    'items_per_page' => $invoices->perPage(),
                    'current_url' => $invoices->url($invoices->currentPage()),
                    'last_url' => $invoices->url($invoices->lastPage()),
                    'previous_url' => $invoices->previousPageUrl(),
                    'next_url' => $invoices->nextPageUrl(),
                    'next_page' => $invoices->hasMorePages() ? $invoices->currentPage() + 1 : null,
                ],
            ], 200);
        } catch (Exception $e) {
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function customerReceipt(Request $request)
    {
        try {
            $id = auth()->user()->id;

            // Aggregate the total amount of pending invoices for the customer
            $invoiceSummary = Invoice::where('invoices.assign', $id)
                ->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->where('invoices.customer', $request->customer)
                ->groupBy('invoices.customer') // Group by customer
                ->select(
                    DB::raw('SUM(COALESCE(invoices.amount, 0)) as totalamount') // Sum the amounts
                )
                ->first(); // Get the aggregated result

            $totalamount =  0;
            $discount = 0;
            $recivedamount = 0;
            $invoicetotal = 0;

            // Get all matching invoices
            $invoices = Invoice::where('invoices.assign', $id)
                ->where('invoices.payment', 'pending') // Only 'pending' invoices
                ->where('invoices.customer', $request->customer)
                ->get();

            foreach ($invoices as $invoicevalue) {
                $recept = Receipt::where('bill_id', $invoicevalue->id)->get();

                foreach ($recept as $receptvalue) {
                    $discount += $receptvalue->discount;
                    $totalamount += $receptvalue->amount;
                }

                $invoicetotal += $invoicevalue->amount;
            }

            $recivedamount = $totalamount + $discount;
            $due = $invoicetotal - $recivedamount;

            $invoiceBillNumber = [];
            foreach ($invoices as $value) {
                $invoiceBillNumber[] = [
                    'invoice_id' => $value->id,
                    'invoice' => $value->invoice,
                ];
            }

            if (!$invoices->count()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Customer found successfully.',
                'remaining_amount' => $due,
                'invoic_list' => $invoiceBillNumber
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
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
                ->select('invoices.*', 'invoices.invoice as bill_number', 'invoices.customer as customers_id', 'invoices.assign as assign_id', 'customers.firm as customers_name' , 'customers.discount as customers_discount' , 'users.full_name as assign_name')
                ->first();
                $receiptamounttotal = Receipt::where('bill_id', $request->invoice)->sum('amount');
                $receiptdiscounttotal = Receipt::where('bill_id', $request->invoice)->sum('discount');
                $discountAmount = $invoice->amount * ($invoice->customers_discount / 100);

                $invoice['amount'] = $invoice['amount'] - ($receiptamounttotal + $receiptdiscounttotal);
                if ($receiptamounttotal == '0') {
                    $invoice["max_discount_amount"] = $invoice->customers_discount;
                }else{ 
                    $invoice["max_discount_amount"] = 0;
                }
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
            $lastReceipt = Receipt::latest('id')->first();

            if($lastReceipt){
                $newReceipt = sprintf('%04d', intval($lastReceipt->receipt) + 1);

            }else{
                $newReceipt = sprintf('%04d', intval(0) + 1);
            }


            $compId = $user->firm_id;
            // Save the User data
            $dataUser = [
                'date' => $request->date,
                'receipt' => $newReceipt,
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

            $invoice = Invoice::find($request->bill_id);

            $customer = Customer::find($invoice->customer);

            $customerDetails = [
                'id' => $customer->id,
                'name' => $customer->firm,
                'city' => $customer->city,
                'phone' => $customer->phone,
            ];

            $ledgerData[] = [
                'date' => Carbon::parse($request->date)->format('d/m/Y'),
                'description' => "Receipt Number " . $newReceipt,
                'receipt' => $request->amount,
                'discount' => $request->discount,
            ];

            usort($ledgerData, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            // Generate the PDF using the 'ledger-pdf' view
            $pdf = Pdf::loadView('recept-pdf', compact('customerDetails', 'ledgerData'));

            // Define the file path to save the PDF in the public/uploads folder
            $filePath = public_path('uploads/receipt_' . $newReceipt . '_' . time() . '.pdf');

            // Ensure the uploads directory exists
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0755, true);
            }

            // Save the PDF to the specified path
            $pdf->save($filePath);

            // Generate the URL for the saved PDF
            $pdfUrl = asset('uploads/' . basename($filePath));


            // Return the response with the customer data
            return response()->json([
                'status' => true,
                'message' => 'Recept Created Succesfully.',
                'invoic_detail' => $receipt,
                'pdf_url' => $pdfUrl
            ], 200);

        } catch (Exception $e) {
            dd($e);
            // Return a response with error details in case of any exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(), // Include the exception message
            ], 500); // Return a 500 internal server error status
        }
    }

    public function customerLeger($ledgerId)
    {
        try {
            // Fetch the customer using the provided ledger ID
            $customer = Customer::find($ledgerId);

            // Check if the customer exists
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.',
                ], 404);
            }

            // Prepare customer details
            $customerDetails = [
                'id' => $customer->id,
                'name' => $customer->firm,
                'city' => $customer->city,
                'phone' => $customer->phone,
            ];

            // Fetch all invoices for the customer
            $invoiceLists = Invoice::where('customer', $ledgerId)
                //->where('invoices.payment', 'pending')
                ->get();

            $ledgerData = []; // To store merged and sorted data
            $totalInvoice = 0; // Total invoice amount
            $totalReceipt = 0; // Total receipt amount

            // Process invoices and related receipts
            foreach ($invoiceLists as $invoice) {
                // Add invoice entry to ledger data
                $ledgerData[] = [
                    'date' => Carbon::parse($invoice->date)->format('d/m/Y'),
                    'description' => "Sales Invoice " . $invoice->invoice,
                    'bill' => $invoice->amount,
                    'receipt' => '0',
                    'discount' => '0',
                ];

                $totalInvoice += $invoice->amount; // Increment total invoice amount

                // Fetch all receipts linked to the invoice
                $receiptLists = Receipt::where('bill_id', $invoice->id)->get();
                foreach ($receiptLists as $receipt) {
                    $ledgerData[] = [
                        'date' => Carbon::parse($receipt->date)->format('d/m/Y'),
                        'description' => "Recepit Voucher " . $receipt->receipt,
                        'bill' => '0',
                        'receipt' => $receipt->amount,
                        'discount' => $receipt->discount,
                    ];

                    $totalReceipt += $receipt->amount + $receipt->discount; // Increment total receipt amount
                }
            }

            // Sort ledger data by date
            usort($ledgerData, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            // Calculate total due
            $totalDue = $totalInvoice - $totalReceipt;

            // Generate the PDF using the 'ledger-pdf' view
            $pdf = Pdf::loadView('ledger-pdf', compact('customerDetails', 'ledgerData', 'totalInvoice', 'totalReceipt', 'totalDue'));

            // Define the file path to save the PDF in the public/uploads folder
            $filePath = public_path('uploads/ledger_' . $ledgerId . '_' . time() . '.pdf');

            // Ensure the uploads directory exists
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0755, true);
            }

            // Save the PDF to the specified path
            $pdf->save($filePath);

            // Generate the URL for the saved PDF
            $pdfUrl = asset('uploads/' . basename($filePath));

            // Return the URL as a response
            return response()->json([
                'status' => true,
                'message' => 'PDF generated successfully.',
                'pdf_url' => $pdfUrl,
            ]);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

}
