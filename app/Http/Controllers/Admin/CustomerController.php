<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerImport;

use Illuminate\Http\Request;


use App\Models\{
    Customer,
    State,
    Invoice,
    Receipt
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $location = Customer::orderBy('id', 'desc')->get();

        $states = State::orderBy('state_id', 'asc')->get();
        // Pass the company and comId to the view
        return view('admin.customer.index', compact('location','states'));
    }

    /**
     * Fetch all companies and return as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        $user = Auth::user();

        $saleparson = Customer::get();

        return response()->json(['data' => $saleparson]);
    }

    /**
     * Update the status of a User.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        try {
            $User = Customer::findOrFail($request->userId);
            $User->status = $request->status;
            $User->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a User by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Customer::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Branch deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|string',
            'firm' => 'required|unique:customers,firm',
            'phone' => 'nullable|numeric|digits:10|unique:customers,phone',
            //'email' => 'nullable|email|unique:customers,email',
            'gst' => 'nullable|unique:customers,gst',
            'address1' => 'required',
            'address2' => 'nullable|string',
            'city' => 'required',
            'state' => 'required|string',
            'discount' => 'required|numeric',
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
            'name' => $request->name,
            'firm' => $request->firm,
            // 'email' => $request->email,
            'phone' => $request->phone,
            'gst' => $request->gst,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'discount' => $request->discount,
        ];
        Customer::create($dataUser);
        return response()->json([
            'success' => true,
            'message' => 'Customer saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = Customer::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|integer|exists:customers,id', // Adjust as needed
            'name' => 'required|string',
            'firm'  => [
                'required',
                Rule::unique('customers', 'firm')->ignore($request->id), // Ensure firm is unique, ignoring the current record
            ],
            // 'email'  => [
            //     'nullable',
            //     Rule::unique('customers', 'email')->ignore($request->id), // Ensure email is unique, ignoring the current record
            // ],
            'phone'  => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('customers', 'phone')->ignore($request->id), // Ensure phone is unique, ignoring the current record
            ],
            'gst'  => [
                'nullable',
                Rule::unique('customers', 'gst')->ignore($request->id), // Ensure GST is unique, ignoring the current record
            ],
            'address1' => 'required',
            'address2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'discount' => 'required',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = Customer::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'Customer Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found']);
    }

    public function lager($id)
    {
        $user = Auth::user();

        $customerDetail = Customer::find($id);

        $invoiceLists = Invoice::where('customer', $id)->get();

        $ledgerData = []; // To store the merged and sorted data
        $totalInvoice = 0; // To calculate the total invoice amount
        $totalReceipt = 0; // To calculate the total receipt amount

        foreach ($invoiceLists as $invoiceValue) {
            // Add invoice data
            $ledgerData[] = [
                'date' => Carbon::parse($invoiceValue->date)->format('d/m/Y'),
                'timestamp' => strtotime($invoiceValue->date), // Store timestamp for sorting
                'description' => "Sales Invoice " . $invoiceValue->invoice,
                'bill' => $invoiceValue->amount,
                'receipt' => '0',
                'discount' => '0',
            ];

            // Update the total invoice amount
            $totalInvoice += $invoiceValue->amount;

            // Fetch related receipts
            $receiptLists = Receipt::where('bill_id', $invoiceValue->id)->get();
            foreach ($receiptLists as $receiptValue) {
                $ledgerData[] = [
                    'date' => Carbon::parse($receiptValue->date)->format('d/m/Y'),
                    'timestamp' => strtotime($receiptValue->date), // Store timestamp for sorting
                    'description' => "Receipt Voucher " . $receiptValue->receipt,
                    'bill' => '0',
                    'receipt' => $receiptValue->amount,
                    'discount' => $receiptValue->discount,
                ];

                // Update the total receipt amount
                $totalReceipt += $receiptValue->amount + $receiptValue->discount;
            }
        }

        // Sort the ledger data by date in **descending** order (newest first)
        usort($ledgerData, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp']; // Sort by timestamp descending
        });

        // Remove timestamp before passing data to the view
        foreach ($ledgerData as &$entry) {
            unset($entry['timestamp']);
        }
        unset($entry); // Break reference

        // Calculate the total due
        $totalDue = $totalInvoice - $totalReceipt;

        // Pass the ledger data and totals to the view
        return view('admin.customer.leger', compact('ledgerData', 'totalInvoice', 'totalReceipt', 'totalDue', 'customerDetail'));
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new CustomerImport, $request->file('file'));

        return back()->with('success', 'Customers Imported Successfully!');
    }
}
