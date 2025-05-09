@extends('admin.layouts.app') 
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Report Sales Person</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" id="buttonpdf">
                Export to PDF
            </button>
        </div>
    </div>
    <div class="row" id="printPDF">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Display Total Outstanding Above the Table -->
                            <div class="mb-3">
                                <strong>Name: </strong>
                                <span>{{ $user->full_name }}</span>
                            </div>
        
        
                            <div class="mb-3">
                                <strong>Total Outstanding: </strong>
                                <span>{{ $salespersonOutstandings->sum(function($total_pending_amount) { return $total_pending_amount->total_pending_amount; }) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                    
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="branchTable">
                            <thead>
                                <tr>
                                    <th>Firm Name</th>
                                    <th>Total Outstanding</th>
                                    <th>Pending Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salespersonOutstandings as $salespersonOutstanding) 
                                    <tr>
                                        <td>{{ $salespersonOutstanding->customer_name }}</td>
                                        <td>{{ $salespersonOutstanding->total_pending_amount }}</td>
                                        <td>{{ $salespersonOutstanding->total_pending_invoices }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('script')
<script>
    const table = $("#branchTable").DataTable({
        processing: true,
        paging: false, // Disable pagination
        searching: false, // Optional: Disable search if not needed
    });
    document.getElementById('buttonpdf').addEventListener('click', function () {
        // Send an AJAX request to generate and download the PDF
        $.ajax({
            url: '{{ route('admin.reports.generate.pdf') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',  // Add CSRF token for security
                user_id: '{{ $user->id }}',   // Pass the user ID to the backend
            },
            xhrFields: {
                responseType: 'blob'  // Set the responseType to 'blob' to handle binary data
            },
            success: function (response) {
                // Trigger the download of the PDF file
                var link = document.createElement('a');
                link.href = URL.createObjectURL(response); // Create an object URL for the blob
                link.download = 'salesperson_report.pdf'; // Specify the name of the file
                link.click(); // Programmatically click the link to download the file
            },
            error: function (error) {
                console.log('Error generating PDF:', error);
            }
        });
    }); 
</script>
@endsection