@extends('admin.layouts.app') @section('style') @endsection @section('content')
@php
    $user = auth()->user();
@endphp
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Receipt</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Receipt
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="startDate">Start Date:</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">End Date:</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="assignNameFilter">Assigned Name:</label>
                            <select id="assignNameFilter" class="form-select">
                                <option value="">All</option>
                                @foreach ($salesparsons as $salesparson)
                                    <option value="{{ $salesparson->full_name }}">{{ $salesparson->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="branchTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No.</th>
                                    <th>Bill No.</th>
                                    <th>Amount</th>
                                    <th>Payment Type</th>
                                    <th>Discount</th>
                                    <th>Sales Parson</th>
                                    <th>Firm</th>
                                    <th>Manager Status</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Receipt Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date"class="form-control" placeholder="Enter Date"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="receipt" class="form-label">Receipt No.</label>
                        <input type="text" id="receipt" class="form-control" value="{{ $newReceipt }}" placeholder="Enter Receipt No."/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer" class="form-label">Firm</label>
                        <select id="customer" class="form-select" onchange="fetchPendingInvoices()">
                            <option value="">Select Firm</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->firm }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bill_id" class="form-label">Bill No.</label>
                        <select id="bill_id" class="form-select">
                            <option value="">Select Invoice</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>                    
                    {{--  <div class="col-md-6 mb-3">
                        <label for="customer" class="form-label">Customer</label>
                        <input type="text" id="customer" class="form-control" placeholder="Enter customer"/>
                        <small class="error-text text-danger"></small>
                    </div>  --}}
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" id="amount" class="form-control" placeholder="Enter Amount"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="given_amount" class="form-label">Given Amount</label>
                        <input type="text" id="given_amount" class="form-control" placeholder="Enter Given Amount"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <input type="hidden" id="givendiscount" class="form-control" placeholder="Enter Discount" value="0.00"/>
                        <input type="text" id="discount" class="form-control" placeholder="Enter Discount" readonly/>
                        <input type="hidden" id="final_amount" class="form-control" placeholder="Enter Final Amount"/>
                        <input type="hidden" id="remaing_amount" class="form-control" placeholder="Enter Remaing Amount"/>
                        <small id= "error-message" class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cal_amount" class="form-label">Amount</label>
                        <input type="text" id="cal_amount" class="form-control" placeholder="Enter Amount"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sales_parson" class="form-label">Sales Parson</label>
                        <input type="text" id="sales_parson" class="form-control" placeholder="Enter Sales Parson"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mode" class="form-label">Mode</label>
                        <select id="mode" class="form-select">
                            <option value="Upi">Upi</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                            <option value="RTGS">RTGS</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddItem">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection @section('script')
<script>
    $(document).on('change', '#bill_id', function () {
        const selectedInvoiceId = $(this).val();
        if (selectedInvoiceId) {
            const url = '{{ route("admin.receipt.detail") }}';
            let data = {
                id: selectedInvoiceId,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'POST',
                data: data,
                success: function (data) {
                    if(data.amount != 0){
                        var discount = parseFloat(data.customers_discount) || 0; // Discount percentage (e.g., 2 for 2%)
                        var amount = parseFloat(data.amount) || 0; // Amount (e.g., 1500000)
                        var givenamount = parseFloat(data.receipts_amount) || 0;
        
                        // Calculate the discount amount
                        var discountAmount = (discount / 100) * amount;
        
                        // Round the discount amount to 2 decimal places
                        discountAmount = data.max_discount_amount;
        
                        // Calculate the final amount after applying the discount
                        var finalAmount = amount - discountAmount;
        
                        // Ensure the given amount does not exceed the final amount
                        
        
                        // Calculate the remaining amount
                        var remainingAmount = finalAmount - givenamount;
    
                        //var FainalAmount = finalAmount - givenamount;
        
                        // Set values in the form fields
                        //$('#customer').val(data.customers_name);
                        $('#sales_parson').val(data.assign_name);
                        $('#amount').val(amount);
                        $('#givendiscount').val(discountAmount + '%'); // Use the rounded discount amount here
                        $('#final_amount').val(remainingAmount);
                        $('#given_amount').val('0');
                        $('#remaing_amount').val(remainingAmount);
                    }else{
                        setFlash("error", "Payment Fully paid and not approved by admin .");
                    }

                },
                error: function (xhr) {
                    setFlash("error", "Invoice not found. Please try again later.");
                }
            });
        } else {
            alert('No Value selected');
        }
    });
    
    // Listen for changes in the given_amount field
    $(document).on('input', '#given_amount', function () {
        var givenamount = parseFloat($(this).val()) || 0;  // Get the updated given amount from the input field
        var finalAmount = parseFloat($('#final_amount').val()) || 0;  // Get the final amount
    
        // Ensure the given amount does not exceed the final amount
        
    
        // Calculate the remaining amount
        var remainingAmount = finalAmount - givenamount;
    
        // Update the remaining amount field
        $('#remaing_amount').val(remainingAmount);
    });
    
           
    $(document).ready(function () {
        // Initialize DataTable
        const table = $("#branchTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.receipt.getall') }}",
            },
            columns: [
                {
                    data: "date",
                    render: (data) => {
                        if (!data) return ""; // Handle null or undefined values
                        const dateObj = new Date(data);
                        return dateObj.toLocaleDateString("en-GB"); // Formats as dd/mm/yyyy
                    },
                },
                {
                    data: "receipt",
                },
                {
                    data: "bill_number",
                },
                {
                    data: "amount",
                },
                {
                    data: "mode",
                },
                {
                    data: "discount",
                },
                {
                    data: "assign_name",
                },
                {
                    data: "customers_name",
                },
                {
                    data: "manager_status",
                    render: (data, type, row) => {
                        const statusBadge = row.manager_status === "active" ?
                            '<span class="badge bg-label-success me-1">Recived</span>' :
                            '<span class="badge bg-label-danger me-1">Pending</span>';
                        return statusBadge;
                    },
                },
                {
                    data: "status",
                    render: (data, type, row) => {
                        const statusBadge = row.status === "active" ?
                            '<span class="badge bg-label-success me-1">Recived</span>' :
                            '<span class="badge bg-label-danger me-1">Pending</span>';
                        return statusBadge;
                    },
                },
                {
                    data: "action",
                    render: (data, type, row) => {
                        @if ($user->role == 'admin')
                            const statusButton = row.status === "inactive"
                            ? `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${row.id}, 'active')">Recived</button>`
                            : `<button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus(${row.id}, 'inactive')">Pending</button>`;
                            const deleteButton = `<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">Delete</button>`;
                            return `${statusButton} ${deleteButton}`;
                        @else
                            const statusButton = row.manager_status === "inactive"
                            ? `<button type="button" class="btn btn-sm btn-success" onclick="updateMangaerStatus(${row.id}, 'active')">Recived</button>`
                            : `<button type="button" class="btn btn-sm btn-danger" onclick="updateMangaerStatus(${row.id}, 'inactive')">Pending</button>`;
                            return `${statusButton}`;
                        @endif
                    },
                },

            ],
            dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: "excelHtml5",
                    title: "Receipt Data",
                    className: "btn btn-success",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8], // Include only specific columns (0-based index)
                    },
                },
                {
                    extend: "pdfHtml5",
                    title: "Receipt Data",
                    className: "btn btn-danger",
                    orientation: "landscape",
                    pageSize: "A4",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8], // Include only specific columns
                    },
                },
            ],
        });

        // Custom date range filter
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const date = data[0]; // Date column index

            if (startDate) {
                const startDateObj = new Date(startDate.split('/').reverse().join('-'));
                const dateObj = new Date(date.split('/').reverse().join('-'));
                if (dateObj < startDateObj) {
                    return false;
                }
            }

            if (endDate) {
                const endDateObj = new Date(endDate.split('/').reverse().join('-'));
                const dateObj = new Date(date.split('/').reverse().join('-'));
                if (dateObj > endDateObj) {
                    return false;
                }
            }

            return true;
        });

        // Assign name filter
        $('#assignNameFilter').on('change', function () {
            const selectedName = $(this).val();
            table.column(6).search(selectedName).draw(); // Assign To column index
        });

        // Trigger filters
        // Trigger filters
        $('#startDate, #endDate').on('change', function () {
            table.draw();
        });

        // Handle form submission via AJAX
        $('#AddItem').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                date: $('#date').val(),
                receipt: $('#receipt').val(),
                bill_id: $('#bill_id').val(),
                amount: $('#cal_amount').val(),
                discount: $('#discount').val(),
                remaing_amount: $('#remaing_amount').val(),
                mode: $('#mode').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('admin.receipt.store') }}', // Adjust the route as necessary
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        setFlash("success", response.message);
                        $('#addModal').modal('hide'); // Close the modal
                        $('#addModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                    } else {
                        // Display validation errors
                        if (response.errors) {
                            for (let field in response.errors) {
                                let $field = $(`#${field}`);
                                if ($field.length) {
                                    $field.siblings('.error-text').text(response.errors[field][0]);
                                }
                            }
                        } else {
                            setFlash("error", response.message);
                        }
                    }
                },
                error: function(xhr) {
                    setFlash("error", "An unexpected error occurred.");
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
        //window.editUser = editUser;
    });

    $(document).ready(function () {
        $('#givendiscount').on('input', function () {
            // Get the values from both fields
            const discount = parseFloat($('#discount').val()) || 0;
            const givendiscount = parseFloat($(this).val()) || 0;

            // Error message element
            const errorMessage = $('#error-message');

            // Check if the given discount exceeds the discount
            if (givendiscount > discount) {
                errorMessage.text("You can't enter more than the discount value "+givendiscount+".");
                $(this).val('0'); // Clear the input field
            } else {
                errorMessage.text(''); // Clear the error message
            }
        });
    });

    function fetchPendingInvoices() {
        let customerId = document.getElementById('customer').value;

        // Clear previous options
        let invoiceSelect = document.getElementById('bill_id');
        invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';

        if (customerId) {
            fetch(`/admin/receipt/get-pending-invoices/${customerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.invoices.forEach(invoice => {
                            let option = document.createElement('option');
                            option.value = invoice.id;
                            option.textContent = invoice.invoice;
                            invoiceSelect.appendChild(option);
                        });
                    } else {
                        alert(data.message || 'Failed to fetch invoices.');
                    }
                })
                .catch(error => console.error('Error fetching invoices:', error));
        }
    }

    document.getElementById('given_amount').addEventListener('input', calculateAmount);
    document.getElementById('discount').addEventListener('input', calculateAmount);

    function calculateAmount() {
        // Get the values from the input fields
        const givenAmount = parseFloat(document.getElementById('given_amount').value) || 0;
        const discount = parseFloat(document.getElementById('givendiscount').value) || 0;
        const discountAmmount = givenAmount * (discount/100)
        console.log(discountAmmount);
        // Calculate the remaining amount
        const calculatedAmount = givenAmount - discountAmmount;

        // Update the cal_amount field
        document.getElementById('cal_amount').value = calculatedAmount.toFixed(2);
        document.getElementById('discount').value = discountAmmount.toFixed(2);
    }

    document.addEventListener("DOMContentLoaded", function () {
        function formatDate(inputId) {
            let input = document.getElementById(inputId);
            input.addEventListener("change", function () {
                let date = new Date(this.value);
                if (!isNaN(date.getTime())) {
                    let formattedDate = ("0" + date.getDate()).slice(-2) + "/" + 
                                        ("0" + (date.getMonth() + 1)).slice(-2) + "/" + 
                                        date.getFullYear();
                    //alert("Selected Date: " + formattedDate); // Display the formatted date
                }
            });
        }
    
        formatDate("startDate");
        formatDate("endDate");
    });
</script>
@endsection
