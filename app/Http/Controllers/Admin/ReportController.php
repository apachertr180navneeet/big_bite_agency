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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
     /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function salePerson()
    {
        $salespersonOutstandings = [];

        $totaloutStandings = 0;

        $salespersons = Invoice::join('users', 'invoices.assign', '=', 'users.id')
            ->where('users.status', 'active')
            ->where('users.role', 'salesparson')
            ->select(
                'users.id',
                'users.full_name'
            )
            ->groupBy('users.id', 'users.full_name')
            ->get();

        foreach ($salespersons as $salekey => $salevalue) {
            // Total pending invoice amount
            $invoicePayments = Invoice::where('assign', $salevalue->id)
                ->where('payment', 'pending')
                ->sum('amount');

            // Total received amount from receipts for the salesperson's invoices
            $receivedAmount = DB::table('receipts')
                ->join('invoices', 'receipts.bill_id', '=', 'invoices.id')
                ->where('invoices.assign', $salevalue->id)
                ->sum('receipts.amount');

            // Calculate outstanding amount
            $outstandingAmount = $invoicePayments - $receivedAmount;

            $salespersonOutstandings[$salekey] = [
                'id' => $salevalue->id,
                'full_name' => $salevalue->full_name,
                'outstanding_amount' => $outstandingAmount,
            ];

            $totaloutStandings +=  $outstandingAmount;
        }

        // Pass the data to the view
        return view('admin.report.index', compact('salespersonOutstandings','totaloutStandings'));
    }


    public function customerWishinvoice($id)
    {
        $user = User::find($id);

        $salespersonOutstandings = Invoice::where('invoices.assign', $id)
        ->where('invoices.payment', 'pending')
        ->join('customers', 'invoices.customer', '=', 'customers.id')
        ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id') // Join receipts table
        ->select(
            'invoices.amount',
            'invoices.id',
            'invoices.invoice',
            'customers.firm',
            DB::raw('COALESCE(invoices.amount, 0) - COALESCE(SUM(receipts.amount), 0) as outstanding') // Subtract receipt amounts
        )
        ->groupBy('invoices.id', 'invoices.amount', 'invoices.invoice', 'customers.firm') // Group by invoice details
        ->havingRaw('COALESCE(invoices.amount, 0) - COALESCE(SUM(receipts.amount), 0) > 0') // Exclude records where outstanding is 0
        ->get(); 
        
        

        // Pass the data to the view
        return view('admin.report.saleparsoncustomer', compact('salespersonOutstandings','user'));
    }


    public function generatePDF(Request $request)
    {
        try {
            // Assuming you're passing the salesperson data via AJAX
            $user = User::find($request->user_id);
            
            // Fetch the salesperson's outstanding invoices data
            $salespersonOutstandings = Invoice::where('invoices.assign', $request->user_id)
            ->where('invoices.payment', 'pending')
            ->join('customers', 'invoices.customer', '=', 'customers.id')
            ->leftJoin('receipts', 'invoices.id', '=', 'receipts.bill_id') // Join receipts table
            ->select(
                'invoices.amount',
                'invoices.id',
                'invoices.invoice',
                'customers.firm',
                DB::raw('COALESCE(invoices.amount, 0) - COALESCE(SUM(receipts.amount), 0) as outstanding') // Subtract receipt amounts
            )
            ->groupBy('invoices.id', 'invoices.amount', 'invoices.invoice', 'customers.firm') // Group by invoice details
            ->havingRaw('COALESCE(invoices.amount, 0) - COALESCE(SUM(receipts.amount), 0) > 0') // Exclude records where outstanding is 0
            ->get();

            // Check if there is any data fetched, if not throw an exception
            if ($salespersonOutstandings->isEmpty()) {
                throw new \Exception('No data found for the requested salesperson.');
            }

            // Generate the PDF using the fetched data
            $pdf = PDF::loadView('admin.report.salesperson_pdf', compact('user', 'salespersonOutstandings'));

            // Return the generated PDF to the browser for download
            return $pdf->download('salesperson_report.pdf');

        } catch (\Exception $e) {
            dd($e);
            // Log the error to the Laravel log file
            Log::error('PDF generation failed: ' . $e->getMessage());

            // Optionally, you can return a response to the user with an error message
            return response()->json(['error' => 'PDF generation failed: ' . $e->getMessage()], 500);
        }
    }

    public function salegeneratePDF()
    {
        $salespersonOutstandings = [];

        $totaloutStanding = 0;

        $salespersons = Invoice::join('users', 'invoices.assign', '=', 'users.id')
            ->where('users.status', 'active')
            ->where('users.role', 'salesparson')
            ->select(
                'users.id',
                'users.full_name'
            )
            ->groupBy('users.id', 'users.full_name')
            ->get();

        foreach ($salespersons as $salekey => $salevalue) {
            // Total pending invoice amount
            $invoicePayments = Invoice::where('assign', $salevalue->id)
                ->where('payment', 'pending')
                ->sum('amount');

            // Total received amount from receipts for the salesperson's invoices
            $receivedAmount = DB::table('receipts')
                ->join('invoices', 'receipts.bill_id', '=', 'invoices.id')
                ->where('invoices.assign', $salevalue->id)
                ->sum('receipts.amount');

            // Calculate outstanding amount
            $outstandingAmount = $invoicePayments - $receivedAmount;

            $salespersonOutstandings[$salekey] = [
                'id' => $salevalue->id,
                'full_name' => $salevalue->full_name,
                'outstanding_amount' => $outstandingAmount,
            ];

            $totaloutStanding +=  $outstandingAmount;
        }
        // Generate the PDF using the fetched data
        $pdf = PDF::loadView('admin.report.indexpdf', compact('salespersonOutstandings','totaloutStanding'));

        // Return the generated PDF to the browser for download
        return $pdf->download('salesperson.pdf');
    }
}
