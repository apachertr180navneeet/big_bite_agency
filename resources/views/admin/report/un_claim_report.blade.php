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
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Un Claim Report</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button id="exportExcelBtn" class="btn btn-primary">Download Excel</button>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="start_date" class="col-md-2 col-form-label">Start Date</label>
                            <input class="form-control" type="date" value="" id="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="col-md-2 col-form-label">End Date</label>
                            <input class="form-control" type="date" value="" id="end_date">
                        </div>
                        <div class="col-md-3" style="margin-top: 4px;">
                            <label for="sale_parson" class="form-label">Sale Parson</label>
                            <select id="sale_parson" class="form-select">
                              <option value="">Select</option>
                              @foreach ($salesparsons as $salesparson)
                                <option value="{{ $salesparson->id }}">{{ $salesparson->full_name }}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="margin-top: 4px;">
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
                        <div class="table-responsive text-nowrap" id="pdfImport">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Bill Number</th>
                                        <th>RTGS</th>
                                        <th>Cash</th>
                                        <th>UPI</th>
                                        <th>Cheque</th>
                                        <th class="no-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="receiptTableBody">
                                    @foreach ($receiptArray as $receipt ) 
                                        <tr>
                                            <td>{{ Carbon::parse($receipt['date'])->format('d/m/Y') }}</td>
                                            <td>{{ $receipt['customers_name'] }}</td>
                                            <td>{{ $receipt['bill_number'] }}</td>
                                            <td>{{ $receipt['RTGS'] }}</td>
                                            <td>{{ $receipt['Cash'] }}</td>
                                            <td>{{ $receipt['UPI'] }}</td>
                                            <td>{{ $receipt['Cheque'] }}</td>
                                            <td class="no-print">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {
        $('#searchBtn').click(function () {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var sale_parson = $('#sale_parson').val();
            var customer = $('#customer').val();
        
            $.ajax({
                url: "{{ route('admin.reports.fetch.receipts') }}", // Laravel route
                type: "GET",
                data: {
                    start_date: start_date,
                    end_date: end_date,
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
                                    <td>${receipt.bill_number}</td>
                                    <td>${receipt.RTGS}</td>
                                    <td>${receipt.Cash}</td>
                                    <td>${receipt.UPI}</td>
                                    <td>${receipt.Cheque}</td>
                                    <td class="no-print">${actionButtons}</td>
                                </tr>
                            `);
                        });
        
                        // Calculate final total after the loop
                        var finaltotal = totalRTGS + totalCash + totalUPI + totalCheque;
        
                        // Append the Total Row at the end
                        tableBody.append(`
                            <tr>
                                <td></td> <!-- Empty column under 'Date' -->
                                <td class="text-end" colspan="1"><strong>Total</strong></td>
                                <td><strong>${finaltotal.toFixed(2)}</strong></td> <!-- Empty column under 'Bill Number' -->
                                <td><strong>${totalRTGS.toFixed(2)}</strong></td>
                                <td><strong>${totalCash.toFixed(2)}</strong></td>
                                <td><strong>${totalUPI.toFixed(2)}</strong></td>
                                <td><strong>${totalCheque.toFixed(2)}</strong></td>
                                <td></td> <!-- Empty column for actions -->
                            </tr>
                        `);
                    } else {
                        tableBody.append('<tr><td colspan="8" class="text-center">No records found</td></tr>');
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
    
    $(document).ready(function () {
        $('#printTableBtn').click(function () {
            var printContent = document.getElementById('pdfImport').innerHTML;
            var originalContent = document.body.innerHTML;
    
            // Retrieve selected salesperson and date
            var salesperson = $('#sale_parson option:selected').text(); // Adjust ID as per your field
            var selectedDate = $('#date').val(); // Adjust ID as per your field
    
            document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                            @media print {
                                body { font-family: Arial, sans-serif; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid black; padding: 8px; text-align: center; }
                                th { background-color: #f2f2f2; }
                                @page { size: A4; margin: 20mm; }
                                .no-print { display: none !important; }
                            }
                        </style>
                    </head>
                    <body>
                        <h2 class="text-center">Unclaim Report</h2>
                        <p><strong>Salesperson:</strong> ${salesperson || 'N/A'}</p>
                        <p><strong>Date:</strong> ${selectedDate || 'N/A'}</p>
                        ${printContent}
                    </body>
                </html>`;
    
            window.print();
            document.body.innerHTML = originalContent;
            location.reload(); // Reload the page to restore functionality
        });
    
        // Export to Excel
        $('#exportExcelBtn').click(function () {
            var table = document.getElementById('pdfImport'); // Adjust ID as per your table
        
            // Clone the table to avoid modifying the original one
            var clonedTable = table.cloneNode(true);
        
            // Remove the last column (assumed to be the "Action" column)
            $(clonedTable).find('tr').each(function () {
                $(this).find('th:last, td:last').remove(); // Removes last column from each row
            });
        
            // Convert table data to an array for better control
            var data = [];
            $(clonedTable).find('tr').each(function () {
                var rowData = [];
                $(this).find('td, th').each(function (index) {
                    var cellText = $(this).text().trim();
        
                    // Convert first column (index 0) to dd/mm/yyyy if in yyyy-mm-dd format
                    if (index === 0 && /^\d{4}-\d{2}-\d{2}$/.test(cellText)) {
                        let [year, month, day] = cellText.split('-');
                        cellText = `${day}/${month}/${year}`; // Convert to DD/MM/YYYY
                    }
        
                    rowData.push(cellText);
                });
                data.push(rowData);
            });
        
            // Convert data array to a worksheet
            var worksheet = XLSX.utils.aoa_to_sheet(data);
        
            // Apply date format to the first column
            var range = XLSX.utils.decode_range(worksheet['!ref']);
            for (let row = range.s.r + 1; row <= range.e.r; row++) { // Skip header row
                let cellAddress = `A${row + 1}`;
                if (worksheet[cellAddress] && worksheet[cellAddress].v) {
                    worksheet[cellAddress].z = 'dd/mm/yyyy'; // Set Excel date format
                }
            }
        
            // Create a workbook and append the sheet
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
        
            // Write to an Excel file
            XLSX.writeFile(workbook, 'Unclaim_Report.xlsx');
        });
        
                
    });
    
          
</script>
@endsection