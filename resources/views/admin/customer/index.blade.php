@extends('admin.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-3 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Customer</span>
            </h5>
        </div>
        <div class="col-md-3 text-center">
            <a href="" class="btn btn-sucesse">
                Import Customer
            </a>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Customer
            </button>
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
                                    <th>Firm Name</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Discount Rate (%)</th>
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
                <button type="button" class="btn btn-primary" id="AddItem">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Customer Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="hidden" id="compid">
                        <label for="editfirm" class="form-label">Firm Name</label>
                        <input type="text" id="editfirm" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editname" class="form-label">Name</label>
                        <input type="text" id="editname" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    {{--  <div class="col-md-6 mb-3">
                        <label for="editemail" class="form-label">Email</label>
                        <input type="text" id="editemail" class="form-control" placeholder="Enter Email" />
                        <small class="error-text text-danger"></small>
                    </div>  --}}
                    <div class="col-md-6 mb-3">
                        <label for="editphone" class="form-label">Phone</label>
                        <input type="text" id="editphone" class="form-control" placeholder="Enter Phone" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editgst" class="form-label">GST No.</label>
                        <input type="text" id="editgst" class="form-control" placeholder="Enter GST No." />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editaddress1" class="form-label">Address 1</label>
                        <input type="text" id="editaddress1" class="form-control" placeholder="Enter Address 1" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editaddress2" class="form-label">Address 2</label>
                        <input type="text" id="editaddress2" class="form-control" placeholder="Enter Address 2" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editcity" class="form-label">City</label>
                        <input type="text" id="editcity" class="form-control" placeholder="Enter City" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editstate" class="form-label">State</label>
                        <select id="editstate" class="form-select">
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->state_name }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                        <small class="error-text text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="editdiscount" class="form-label">Discount</label>
                        <select id="editdiscount" class="form-select">
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
                <button type="button" class="btn btn-primary" id="EditComapany">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection @section('script')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        const baseUrl = "{{ route('admin.customer.lager', ['id' => ':id']) }}";
        const table = $("#branchTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.customer.getall') }}",
            },
            columns: [
                {
                    data: "firm",
                },
                {
                    data: "name",
                },
                {
                    data: "phone",
                },
                {
                    data: "discount",
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
                        const statusButton = row.status === "inactive"
                            ? `<button type="button" class="btn btn-sm btn-success" onclick="updateUserStatus(${row.id}, 'active')">Activate</button>`
                            : `<button type="button" class="btn btn-sm btn-danger" onclick="updateUserStatus(${row.id}, 'inactive')">Deactivate</button>`;

                        //const deleteButton = `<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">Delete</button>`;
                        const editButton = `<button type="button" class="btn btn-sm btn-warning" onclick="editUser(${row.id})">Edit</button>`;

                        const viewLeger = `<a href="${baseUrl.replace(':id', row.id)}" class="btn btn-sm btn-success">Ledger</a>`;

                        return `${statusButton} ${editButton} ${viewLeger}`;
                    },
                },

            ],
        });

        // Handle form submission via AJAX
        $('#AddItem').click(function(e) {
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
            const url = '{{ route("admin.customer.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#compid').val(data.id);
                    $('#editfirm').val(data.firm);
                    $('#editname').val(data.name);
                    $('#editemail').val(data.email);
                    $('#editphone').val(data.phone);
                    $('#editgst').val(data.gst);
                    $('#editaddress1').val(data.address1);
                    $('#editaddress2').val(data.address2);
                    $('#editcity').val(data.city);
                    $('#editstate').val(data.state);
                    $('#editdiscount').val(data.discount);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Customer found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Customer not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditComapany').on('click', function() {
            const userId = $('#compid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('admin.customer.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    firm: $('#editfirm').val(),
                    name: $('#editname').val(),
                    email: $('#editemail').val(),
                    phone: $('#editphone').val(),
                    gst: $('#editgst').val(),
                    address1: $('#editaddress1').val(),
                    address2: $('#editaddress2').val(),
                    city: $('#editcity').val(),
                    state: $('#editstate').val(),
                    discount: $('#editdiscount').val(),
                    id: userId // Ensure userId is in scope or adjust accordingly
                },
                success: function(response) {
                    console.log(response);
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
                    console.error('Error updating Customer data:', xhr);
                }
            });
        });

        // Update user status
        function updateUserStatus(userId, status) {
            const message = status === "active" ? "Sales Parson will be able to log in after activation." : "Sales Parson will not be able to log in after deactivation.";

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
                        url: "{{ route('admin.customer.status') }}",
                        data: { userId, status, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                const successMessage = status === "active" ? "Sales Parson activated successfully." : "Sales Parson deactivated successfully.";
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
                    const url = '{{ route("admin.customer.destroy", ":userId") }}'.replace(":userId", userId);
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
        window.deleteUser = deleteUser;
        window.editUser = editUser;
    });

</script>
@endsection
