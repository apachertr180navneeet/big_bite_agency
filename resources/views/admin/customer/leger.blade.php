@extends('admin.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Customer</span>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="branchTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Bill</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledgerData as $ledger)
                                    <tr>
                                        <td>{{ htmlspecialchars($ledger['date'], ENT_QUOTES, 'UTF-8') }}</td>
                                        <td>{{ htmlspecialchars($ledger['description'], ENT_QUOTES, 'UTF-8') }}</td>
                                        <td>{{ number_format($ledger['bill'], 2) }}</td>
                                        <td>{{ number_format($ledger['receipt'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total Due</strong></td>
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

@endsection
