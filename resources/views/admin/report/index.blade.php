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
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Total Outstanding: </strong>
                                <span>{{ $totaloutStandings }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="branchTable">
                            <thead>
                                <tr>
                                    <th>Sale Parson</th>
                                    <th>Total Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salespersonOutstandings as $salespersonOutstanding ) 
                                    <tr>
                                        <td><a href="{{ route('admin.reports.customer.invoice', $salespersonOutstanding['id']) }}">{{ $salespersonOutstanding['full_name'] }}</a></td>
                                        <td>{{ $salespersonOutstanding['outstanding_amount'] }}</td>
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
    document.getElementById('buttonpdf').addEventListener('click', function () {
        // Send an AJAX request to generate and download the PDF
        $.ajax({
            url: '{{ route('admin.reports.sale.generate.pdf') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',  // Add CSRF token for security
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