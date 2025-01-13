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


        // Pass the data to the view
        return view('admin.receipt.index', compact('customers','salesparsons','invoices'));
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
        ->select('receipts.*', 'invoices.invoice as bill_number', 'invoices.customer as customers_id', 'invoices.assign as assign_id', 'customers.name as customers_name' , 'users.full_name as assign_name')
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
            Receipt::where('id', $id)->delete();

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
            'invoice' => 'required|unique:invoices,invoice',
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
        Receipt::create($dataUser);
        return response()->json([
            'success' => true,
            'message' => 'Invoice saved successfully!',
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
}
