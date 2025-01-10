<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    User,
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;
use Illuminate\Validation\Rule;

class SalesparsonmanagmentController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $compId = $user->firm_id;

        $location = User::where('role','user')->orderBy('id', 'desc')->get();
        // Pass the company and comId to the view
        return view('admin.salesparson.index', compact('location'));
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

        $saleparson = User::where('role', 'salesparson')
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
            $User = User::findOrFail($request->userId);
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
            User::where('id', $id)->delete();

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
            'full_name' => 'required|string',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|unique:users,email',
            'address' => 'required',
            'dob' => 'required',
            'alternative_phone' => 'required|unique:users,alternative_phone',
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
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'alternative_phone' => $request->alternative_phone,
        ];
        User::create($dataUser);
        return response()->json([
            'success' => true,
            'message' => 'Sales Parson saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $request->validate([
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
        ]);

        $user = User::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'Branch Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Branch not found']);
    }
}