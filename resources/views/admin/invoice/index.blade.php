@extends('admin.layouts.app') @section('style') @endsection @section('content')
@php
    $user = auth()->user();
@endphp
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Invoice</span>
            </h5>
        </div>
        <div class="col-md-3 text-center">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                Import Invoice
            </button>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Invoice
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
                                    <th>Invoice</th>
                                    <th>Firm Name</th>
                                    <th>Assign To</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
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
                <h5 class="modal-title" id="exampleModalLabel1">Invoice Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date"class="form-control" placeholder="Enter Date"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="invoice" class="form-label">Invoice No.</label>
                        <input type="text" id="invoice" class="form-control" placeholder="Enter Invoice No."/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="customer" class="form-label">Firm Name 
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModalCustomer">
                                Add Firm
                            </button>
                        </label>
                        <select id="customer" class="form-select js-example-basic-single">
                            <option value="">Select Firm</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->firm }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="assign" class="form-label">Sales Parson Name</label>
                        <select id="assign" class="form-select js-example-basic-single">
                            <option value="">Select Sales Parson</option>
                            @foreach ($salesparsons as $salesparson)
                                <option value="{{ $salesparson->id }}">{{ $salesparson->full_name }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" id="amount" class="form-control" placeholder="Enter Amount"/>
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Invoice Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input type="hidden" id="compid">
                        <label for="editdate" class="form-label">Date</label>
                        <input type="text" id="editdate"class="form-control" placeholder="Enter Date"/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="editinvoice" class="form-label">Invoice No.</label>
                        <input type="text" id="editinvoice" class="form-control" placeholder="Enter Invoice No."/>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="editcustomer" class="form-label">Customer Name</label>
                        <select id="editcustomer" class="form-select js-example-basic-single">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="editassign" class="form-label">Sales Parson Name</label>
                        <select id="editassign" class="form-select js-example-basic-single">
                            <option value="">Select Sales Parson</option>
                            @foreach ($salesparsons as $salesparson)
                                <option value="{{ $salesparson->id }}">{{ $salesparson->full_name }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="editamount" class="form-label">Amount</label>
                        <input type="text" id="editamount" class="form-control" placeholder="Enter Amount"/>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="EditComapany">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModalCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Customer Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firm" class="form-label">Firm Name</label>
                        <input type="text" id="firm" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    {{--  <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" id="email" class="form-control" placeholder="Enter Email" />
                        <small class="error-text text-danger"></small>
                    </div>  --}}
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" class="form-control" placeholder="Enter Phone" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gst" class="form-label">GST No.</label>
                        <input type="text" id="gst" class="form-control" placeholder="Enter GST No." />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address1" class="form-label">Address 1</label>
                        <input type="text" id="address1" class="form-control" placeholder="Enter Address 1" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address2" class="form-label">Address 2</label>
                        <input type="text" id="address2" class="form-control" placeholder="Enter Address 2" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" class="form-control" placeholder="Enter City" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">State</label>
                        <select id="state" class="form-select">
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->state_name }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <select id="discount" class="form-select">
                            <option value="0">0%</option>
                            <option value="1">1%</option>
                            <option value="2">2%</option>
                            <option value="3">3%</option>
                            <option value="4">4%</option>
                            <option value="5">5%</option>
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddCustomer">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Import Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.invoice.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Import</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection @section('script')
<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 5 theme
        $('.js-example-basic-single').select2({
            //theme: 'bootstrap-5' // Apply Bootstrap 5 theme
        });
    
        // Reinitialize Select2 when modal is shown
        $('#addModal').on('shown.bs.modal', function () {
            $('.js-example-basic-single').select2({
                //theme: 'bootstrap-5',
                dropdownParent: $('#addModal') // Ensures dropdown stays inside modal
            });
        });

        $('#editModal').on('shown.bs.modal', function () {
            $('.js-example-basic-single').select2({
                //theme: 'bootstrap-5',
                dropdownParent: $('#editModal') // Ensures dropdown stays inside modal
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        const userRole = @json(auth()->check() ? auth()->user()->role : null);
        console.log(userRole);        
        // Initialize DataTable
        const table = $("#branchTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.invoice.getall') }}",
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
                    data: "invoice",
                },
                {
                    data: "customers_name",
                },
                {
                    data: "assign_name",
                },
                {
                    data: "amount",
                },
                {
                    data: "payment",
                    render: (data, type, row) => {
                        const statusBadge = row.payment === "done" ?
                            '<span class="badge bg-label-success me-1">Done</span>' :
                            '<span class="badge bg-label-danger me-1">Pending</span>';
                        return statusBadge;
                    },
                },
                {
                    data: "status",
                    render: (data, type, row) => {
                        const statusBadge = row.status === "active" ?
                            '<span class="badge bg-label-success me-1">Active</span>' :
                            '<span class="badge bg-label-danger me-1">Inactive</span>';
                        return statusBadge;
                    },
                },
                {
                    data: "action",
                    render: (data, type, row) => {
                        let statusButton = "";
                        let editButton = "";

                        // Ensure userRole is not null
                        if (userRole && (userRole === "admin")) {
                            statusButton = row.status === "inactive"
                                ? `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${row.id}, 'active')">Activate</button>`
                                : `<button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus(${row.id}, 'inactive')">Deactivate</button>`;
                            if(row.payment === "done"){
                                editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editUser(${row.id})">Edit</button>`;

                            }else{
                                editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editUser(${row.id})">Edit</button>`;
                            }
                        } else if (userRole && (userRole === "manger")) {
                            statusButton = row.status === "inactive"
                                ? `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${row.id}, 'active')">Activate</button>`
                                : `<button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus(${row.id}, 'inactive')">Deactivate</button>`;

                                if(row.payment === "pending"){
                                    editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editUser(${row.id})">Edit</button>`;
    
                                }else{
                                    editButton = "";
                                }
                        }

                        return `${statusButton} ${editButton}`;
                    }
                },

            ],
            dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: "excelHtml5",
                    title: "Invoice Data",
                    className: "btn btn-success",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Include only specific columns (0-based index)
                    },
                },
                {
                    extend: "pdfHtml5",
                    title: "Invoice Data",
                    className: "btn btn-danger",
                    orientation: "landscape",
                    pageSize: "A4",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Include only specific columns
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
            table.column(3).search(selectedName).draw(); // Assign To column index
        });

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
                invoice: $('#invoice').val(),
                customer: $('#customer').val(),
                assign: $('#assign').val(),
                amount: $('#amount').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('admin.invoice.store') }}', // Adjust the route as necessary
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

        // Define editUser function
        function editUser(userId) {
            const url = '{{ route("admin.invoice.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#compid').val(data.id);
                    $('#editdate').val(data.date);
                    $('#editinvoice').val(data.invoice);
                    $('#editcustomer').val(data.customer);
                    $('#editassign').val(data.assign);
                    $('#editamount').val(data.amount);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Invoice found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Invoice not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditComapany').on('click', function() {
            const userId = $('#compid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('admin.invoice.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: $('#editdate').val(),
                    invoice: $('#editinvoice').val(),
                    customer: $('#editcustomer').val(),
                    assign: $('#editassign').val(),
                    amount: $('#editamount').val(),
                    id: userId // Ensure userId is in scope or adjust accordingly
                },
                success: function(response) {
                    if (response.success == true) {
                        // Optionally, refresh the page or update the table with new data
                        //table.ajax.reload();
                        setFlash("success", response.message);
                        $('#editModal').modal('hide'); // Close the modal
                        $('#editModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                    } else {
                        for (let field in response.errors) {
                            let $field = $(`#edit${field}`);
                            if ($field.length) {
                                $field.siblings('.error-text').text(response.errors[field][0]);
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error updating Invoice data:', xhr);
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
                        url: "{{ route('admin.invoice.status') }}",
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
                    const url = '{{ route("admin.invoice.destroy", ":userId") }}'.replace(":userId", userId);
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

        // Handle form submission via AJAX
        $('#AddCustomer').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                firm: $('#firm').val(),
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                gst: $('#gst').val(),
                address1: $('#address1').val(),
                address2 : $('#address2').val(),
                city : $('#city').val(),
                state : $('#state').val(),
                discount : $('#discount').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('admin.customer.store') }}', // Adjust the route as necessary
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        setFlash("success", response.message);
                        $('#addModal').modal('hide'); // Close the modal
                        $('#addModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                        location.reload();
                        $('#addModalCustomer').modal('hide'); // Show the modal
                        $('#addModal').modal('show'); // Show the modal
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

         // Flash message function using Toast.fire
         function setFlash(type, message) {
            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Expose functions to global scope
        window.updateUserStatus = updateUserStatus;
        window.deleteUser = deleteUser;
        window.editUser = editUser;
    });

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
