@extends('admin.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Ledger</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" id="Print">Print</button>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card" id="divPrint">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                        </div>
                        <div class="col-md-4 text-center mb-2">
                            <img src="{{asset('assets/admin/img/bigbitelogo.png')}}" width="35%"/>
                        </div>
                        <div class="col-md-4 mb-2">
                        </div>
                        <h4 class="text-center">Firm :- {{ $customerDetail->firm }}</h4>
                        <h4 class="text-center">Mobile :- {{ $customerDetail->phone }}</h4>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="branchTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Bill</th>
                                    <th>Receipt</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledgerData as $ledger)
                                    <tr>
                                        <td>{{ htmlspecialchars($ledger['date'], ENT_QUOTES, 'UTF-8') }}</td>
                                        <td>{{ htmlspecialchars($ledger['description'], ENT_QUOTES, 'UTF-8') }}</td>
                                        <td>{{ number_format($ledger['bill'], 2) }}</td>
                                        <td>{{ number_format($ledger['receipt'], 2) }}</td>
                                        <td>{{ number_format($ledger['discount'], 2) }}</td>
                                    </tr>
                                @endforeach
                                {{--  <tr>
                                    <td colspan="3" class="text-end"><strong>Total Invoice</strong></td>
                                    <td colspan="2"><strong>{{ number_format($totalInvoice, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Receipt</strong></td>
                                    <td colspan="2"><strong>{{ number_format($totalReceipt, 2) }}</strong></td>
                                </tr>  --}}
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Due</strong></td>
                                    <td colspan="2"><strong>{{ number_format($totalDue, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection @section('script')
<script>
    document.getElementById('Print').addEventListener('click', function () {
        // Get the content of the div to print
        var printContent = document.getElementById('divPrint').innerHTML;
    
        // Open a new window for printing
        var printWindow = window.open('', '', 'width=900,height=600');
    
        // Add the content to the new window with proper CSS linking
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
    
        // Ensure styles are loaded before printing
        printWindow.document.close(); // Close document to finish writing
    
        // Wait for styles to load before printing
        printWindow.onload = function () {
            printWindow.focus(); // Focus the window before printing
            printWindow.print(); // Trigger print dialog
            //printWindow.close(); // Close the window after printing
        };
    });
       
</script>

@endsection
