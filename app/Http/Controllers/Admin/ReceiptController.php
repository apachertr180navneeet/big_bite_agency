<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Customer,
    User,
    Invoice,
    Receipt
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $customers = Customer::where('status','active')->get();

        $salesparsons = User::where('status','active')->where('role','salesparson')->get();

        $invoices = Invoice::get();

        $lastReceipt = Receipt::latest('id')->first();

        if($lastReceipt){
            $newReceipt = sprintf('%04d', intval($lastReceipt->receipt) + 1);
        }else{
            $newReceipt = sprintf('%04d', intval(1));
        }


        // Pass the data to the view
        return view('admin.receipt.index', compact('customers','salesparsons','invoices','newReceipt'));
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

        $saleparson = Receipt::join('invoices', 'receipts.bill_id', '=', 'invoices.id')
        ->join('users', 'invoices.assign', '=', 'users.id')
        ->join('customers', 'invoices.customer', '=', 'customers.id')
        ->select('receipts.*', 'invoices.invoice as bill_number', 'invoices.customer as customers_id', 'invoices.assign as assign_id', 'customers.firm as customers_name' , 'users.full_name as assign_name')
        ->get();

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
            $User = Receipt::findOrFail($request->userId);

            $totalrececitamount = Receipt::where('bill_id',$User->bill_id)->get();
            $totalamount = 0;
            $totalDiscount = 0;
            $finalAmount = 0;
            foreach ($totalrececitamount as $key => $value) {
                $totalamount += $value->amount;
                $totalDiscount += $value->discount;

                $finalAmount  = $totalamount + $totalDiscount;
            }
            //if($User->full_payment == 'yes'){
                $Bill = Invoice::findOrFail($User->bill_id);
                if($Bill->amount - $finalAmount == 0){
                    if($Bill->payment == 'pending'){
                        $Bill->payment = 'done';
                    }else{
                        $Bill->payment == 'pending';
                    }
                    $Bill->save();
                }
            //}
            $User->manager_status = 'active';
            $User->status = $request->status;
            $User->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function managerStatus(Request $request)
    {
        try {
            $User = Receipt::findOrFail($request->userId);
            $User->manager_status = $request->status;
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
            Receipt::where('id', $id)->forceDelete();

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
            'date' => 'required|string',
            'receipt' => 'required|unique:receipts,receipt',
            'bill_id' => 'required',
            'amount' => 'required',
            'discount' => 'required',
            'mode' => 'required',
            'remaing_amount' => 'required',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $invoices = Invoice::where('id', $request->bill_id)->first(); // Select only necessary fields

        
        $customers = Customer::where('id', $invoices->customer)->first();
        
        $customerNumber = $customers->phone;
        $customerName = $customers->name;
        $firmName = $customers->firm;
        $formattedDate = date('d-m-Y', strtotime($request->date));
        
        $user = Auth::user();

        $compId = $user->firm_id;
        // Save the User data
        $dataUser = [
            'date' => $request->date,
            'receipt' => $request->receipt,
            'bill_id' => $request->bill_id,
            'assign' => $request->assign,
            'amount' => $request->amount,
            'discount' => $request->discount,
            'mode' => $request->mode,
            'remaing_amount' => $request->remaing_amount,
        ];
        Receipt::create($dataUser);

        // Prepare SMS details
        $authKey = "SYSPOLYSALES";
        $mobileNumber = $customerNumber; // Replace with actual number
        //$message = "Your receipt has been successfully recorded. Receipt No: " . $request->receipt . ", Amount: " . $request->amount;
        $message = "Dear $customerName, ($firmName), We have received payment today $formattedDate. Total amount of receipt is $request->amount and receipt no is $request->receipt. Thanks for your payment.";

        $url = "https://wywspl.com/sendMessage.php";

        $postData = [
            'AUTH_KEY' => $authKey,
            'phone' => $mobileNumber,
            'message' => $message,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            // \Log::error('SMS sending error: ' . curl_error($ch));
            dd($ch);
        }

        curl_close($ch);
        return response()->json([
            'success' => true,
            'message' => 'Receipt saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = Receipt::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $rules = [
            'date' => 'required|string',
            'invoice'  => [
                'required',
                Rule::unique('invoices', 'invoice')->ignore($request->id), // Ensure account number is unique, ignoring the current record
            ],
            'customer' => 'required',
            'assign' => 'required',
            'amount' => 'required',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = Receipt::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'Invoice Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Invoice not found']);
    }

    // Get invoice details
    public function detail(Request $request)
    {
        // Validate the request to ensure 'invoice' is present
        $request->validate([
            'id' => 'required|integer',
        ]);

        $invoiceId = $request->id;

        // Query to fetch the invoice with joins
        $invoice = Invoice::where('invoices.id', $invoiceId)
            ->join('users', 'invoices.assign', '=', 'users.id')
            ->join('customers', 'invoices.customer', '=', 'customers.id')
            ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id')
            ->select(
                'invoices.*',
                'invoices.invoice as bill_number',
                'invoices.customer as customers_id',
                'invoices.assign as assign_id',
                'customers.name as customers_name',
                'customers.discount as customers_discount',
                'users.full_name as assign_name'
            )
            ->first();

        // Check if invoice was found
        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found.',
            ], 404);
        }
        

        // Calculate receipt totals
        $receiptAmountTotal = Receipt::where('bill_id', $invoiceId)->sum('amount');
        $receiptDiscountTotal = Receipt::where('bill_id', $invoiceId)->sum('discount');

        // Calculate discount amount
        $discountAmount = $invoice->amount * ($invoice->customers_discount / 100);

        // Adjust the invoice amount
        $invoice['amount'] -= ($receiptAmountTotal + $receiptDiscountTotal);

        // Determine max discount amount
        $invoice["max_discount_amount"] = $invoice->customers_discount;

        return response()->json($invoice);
    }


    public function getPendingInvoices($customerId)
    {
        try {
            $invoices = Invoice::where('customer', $customerId)
                ->where('payment', 'pending') // Adjust the condition based on your status column
                ->get(['id', 'invoice']); // Select only necessary fields

            return response()->json(['success' => true, 'invoices' => $invoices]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}
