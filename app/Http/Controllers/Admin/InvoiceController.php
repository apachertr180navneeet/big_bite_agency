<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    Customer,
    User,
    Invoice
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get only the current date
        $currentDate = Carbon::now()->toDateString();

        // Get the last invoice
        $lastInvoice = Invoice::latest('invoice')->first();

        if ($lastInvoice) {
            // Increment the last invoice number
            $newInvoice = $lastInvoice->invoice + 1;
        } else {
            // Start with 1 if no invoices exist
            $newInvoice = 1;
        }

        // Format the invoice number with leading zeros (4 digits)
        $formattedInvoice = str_pad($newInvoice, 4, '0', STR_PAD_LEFT);

        $customers = Customer::where('status','active')->get();

        $salesparsons = User::where('status','active')->where('role','salesparson')->get();


        // Pass the data to the view
        return view('admin.invoice.index', compact('currentDate', 'formattedInvoice','customers','salesparsons'));
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

        $saleparson = Invoice::join('customers', 'invoices.customer', '=', 'customers.id')
        ->join('users', 'invoices.assign', '=', 'users.id')
        ->select('invoices.*', 'customers.name as customers_name' , 'users.full_name as assign_name')
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
            $User = Invoice::findOrFail($request->userId);
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
            Invoice::where('id', $id)->delete();

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
            'invoice' => 'required|unique:users,phone',
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

        $user = Auth::user();

        $compId = $user->firm_id;
        // Save the User data
        $dataUser = [
            'date' => $request->date,
            'invoice' => $request->invoice,
            'customer' => $request->customer,
            'assign' => $request->assign,
            'amount' => $request->amount,
        ];
        Invoice::create($dataUser);
        return response()->json([
            'success' => true,
            'message' => 'Invoice saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = Invoice::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|integer|exists:users,id', // Adjust as needed
            'full_name' => 'required|string',
            'phone'  => [
                'required',
                Rule::unique('users', 'phone')->ignore($request->id), // Ensure account number is unique, ignoring the current record
            ],
            'email'  => [
                'required',
                Rule::unique('users', 'email')->ignore($request->id), // Ensure account number is unique, ignoring the current record
            ],
            'address' => 'required',
            'dob' => 'required',
            'alternative_phone'  => [
                'required',
                Rule::unique('users', 'alternative_phone')->ignore($request->id), // Ensure account number is unique, ignoring the current record
            ],
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = Invoice::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'Branch Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Branch not found']);
    }
}
