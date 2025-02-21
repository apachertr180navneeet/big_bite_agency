@extends('admin.layouts.app') 
@section('content')
@php
    use Carbon\Carbon;

    // Calculate totals
    $totalRTGS = $receiptArray->sum('RTGS');
    $totalCash = $receiptArray->sum('Cash');
    $totalUPI = $receiptArray->sum('UPI');
    $totalCheque = $receiptArray->sum('Cheque');

    $user = auth()->user();
@endphp
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Un Claim Report</span>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="date" class="col-md-2 col-form-label">Date</label>
                            <input class="form-control" type="date" value="" id="date">
                        </div>
                        <div class="col-md-4" style="margin-top: 4px;">
                            <label for="sale_parson" class="form-label">Sale Parson</label>
                            <select id="sale_parson" class="form-select">
                              <option value="">Select</option>
                              @foreach ($salesparsons as $salesparson)
                                <option value="{{ $salesparson->id }}">{{ $salesparson->full_name }}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-4" style="margin-top: 4px;">
                            <label for="customer" class="form-label">Customer</label>
                            <select id="customer" class="form-select">
                              <option value="">Select</option>
                              @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->firm }}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 text-end mt-2">
                            <button type="button" id="searchBtn" class="btn rounded-pill btn-primary">Search</button>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>RTGS</th>
                                        <th>Cash</th>
                                        <th>UPI</th>
                                        <th>Cheque</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="receiptTableBody">
                                    @foreach ($receiptArray as $receipt ) 
                                        <tr>
                                            <td>{{ Carbon::parse($receipt['date'])->format('d/m/Y') }}</td>
                                            <td>{{ $receipt['customers_name'] }}</td>
                                            <td>{{ $receipt['RTGS'] }}</td>
                                            <td>{{ $receipt['Cash'] }}</td>
                                            <td>{{ $receipt['UPI'] }}</td>
                                            <td>{{ $receipt['Cheque'] }}</td>
                                            <td>
                                                @if ($user->role == 'admin')
                                                    @if ($receipt['status'] == 'inactive')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus({{ $receipt['id'] }}, 'active')">Recived</button>
                                                    {{--  @else
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus({{ $receipt['id'] }}, 'inactive')">Pending</button>  --}}
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser({{ $receipt['id'] }})">Delete</button>
                                                @else
                                                    @if ($receipt['manager_status'] == 'inactive')
                                                      <button type="button" class="btn btn-sm btn-success" onclick="updateMangaerStatus({{ $receipt['id'] }}, 'active')">Recived</button>
                                                    {{--  @else
                                                      <button type="button" class="btn btn-sm btn-danger" onclick="updateMangaerStatus({{ $receipt['id'] }}, 'inactive')">Pending</button>  --}}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Total Row -->
                                    <tr>
                                        <td></td> <!-- Empty column under 'Name' -->
                                        <td class="text-end" colspan="1"><strong>Total</strong></td>
                                        <td><strong>{{ $totalRTGS }}</strong></td>
                                        <td><strong>{{ $totalCash }}</strong></td>
                                        <td><strong>{{ $totalUPI }}</strong></td>
                                        <td><strong>{{ $totalCheque }}</strong></td>
                                        <td></td> <!-- Empty column under 'Name' -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#searchBtn').click(function () {
            var date = $('#date').val();
            var sale_parson = $('#sale_parson').val();
            var customer = $('#customer').val();
        
            $.ajax({
                url: "{{ route('admin.reports.fetch.receipts') }}", // Laravel route
                type: "GET",
                data: {
                    date: date,
                    sale_parson: sale_parson,
                    customer: customer
                },
                success: function (response) {
                    var tableBody = $('#receiptTableBody');
                    tableBody.empty(); // Clear existing data
        
                    var totalRTGS = 0, totalCash = 0, totalUPI = 0, totalCheque = 0;
        
                    if (response.data.length > 0) {
                        $.each(response.data, function (index, receipt) {
                            var formattedDate = new Date(receipt.date).toLocaleDateString('en-GB'); // Convert to dd/mm/yyyy format
                            
                            // Accumulate totals
                            totalRTGS += parseFloat(receipt.RTGS) || 0;
                            totalCash += parseFloat(receipt.Cash) || 0;
                            totalUPI += parseFloat(receipt.UPI) || 0;
                            totalCheque += parseFloat(receipt.Cheque) || 0;
        
                            // Determine user role and button display
                            var actionButtons = '';
        
                            if ("{{ auth()->user()->role }}" === 'admin') {
                                if (receipt.status === 'inactive') {
                                    actionButtons += `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${receipt.id}, 'active')">Received</button>`;
                                }
                                actionButtons += `<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${receipt.id})">Delete</button>`;
                            } else {
                                if (receipt.manager_status === 'inactive') {
                                    actionButtons += `<button type="button" class="btn btn-sm btn-success" onclick="updateManagerStatus(${receipt.id}, 'active')">Received</button>`;
                                }
                            }
        
                            tableBody.append(`
                                <tr>
                                    <td>${formattedDate}</td>
                                    <td>${receipt.customers_name}</td>
                                    <td>${receipt.RTGS}</td>
                                    <td>${receipt.Cash}</td>
                                    <td>${receipt.UPI}</td>
                                    <td>${receipt.Cheque}</td>
                                    <td>${actionButtons}</td>
                                </tr>
                            `);
                        });
        
                        // Append the Total Row at the end
                        tableBody.append(`
                            <tr>
                                <td></td> <!-- Empty column under 'Name' -->
                                <td class="text-end" colspan="1"><strong>Total</strong></td>
                                <td><strong>${totalRTGS.toFixed(2)}</strong></td>
                                <td><strong>${totalCash.toFixed(2)}</strong></td>
                                <td><strong>${totalUPI.toFixed(2)}</strong></td>
                                <td><strong>${totalCheque.toFixed(2)}</strong></td>
                                <td></td> <!-- Empty column for actions -->
                            </tr>
                        `);
                    } else {
                        tableBody.append('<tr><td colspan="7" class="text-center">No records found</td></tr>');
                    }
                }
            });
        });
        

        // Update user status
        function updateUserStatus(userId, status) {
            const message = status === "active" ? "Invoice will be able to log in after activation." : "Invoice will not be able to log in after deactivation.";

            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Okay",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.receipt.status') }}",
                        data: { userId, status, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                const successMessage = status === "active" ? "Invoice activated successfully." : "Invoice deactivated successfully.";
                                setFlash("success", successMessage);
                            } else {
                                setFlash("error", "There was an issue changing the status. Please contact your system administrator.");
                            }
                            table.ajax.reload(); // Reload DataTable
                        },
                        error: function () {
                            setFlash("error", "There was an issue processing your request. Please try again later.");
                        },
                    });
                } else {
                    table.ajax.reload(); // Reload DataTable
                }
            });
        };

        function updateMangaerStatus(userId, status) {
            const message = status === "active" ? "Invoice will be able to log in after activation." : "Invoice will not be able to log in after deactivation.";

            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Okay",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.receipt.manager.status') }}",
                        data: { userId, status, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                const successMessage = status === "active" ? "Invoice activated successfully." : "Invoice deactivated successfully.";
                                setFlash("success", successMessage);
                            } else {
                                setFlash("error", "There was an issue changing the status. Please contact your system administrator.");
                            }
                            table.ajax.reload(); // Reload DataTable
                        },
                        error: function () {
                            setFlash("error", "There was an issue processing your request. Please try again later.");
                        },
                    });
                } else {
                    table.ajax.reload(); // Reload DataTable
                }
            });
        };

        // Delete user
        function deleteUser(userId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = '{{ route("admin.receipt.destroy", ":userId") }}'.replace(":userId", userId);
                    $.ajax({
                        type: "DELETE",
                        url,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                setFlash("success", "User deleted successfully.");
                            } else {
                                setFlash("error", "There was an issue deleting the user. Please contact your system administrator.");
                            }
                            table.ajax.reload(); // Reload DataTable
                        },
                        error: function () {
                            setFlash("error", "There was an issue processing your request. Please try again later.");
                        },
                    });
                }
            });
        };

         // Flash message function using Toast.fire
         function setFlash(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Expose functions to global scope
        window.updateUserStatus = updateUserStatus;
        window.updateMangaerStatus = updateMangaerStatus;
        window.deleteUser = deleteUser;
    });        
</script>
@endsection