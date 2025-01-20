@extends('admin.layouts.app') @section('style') @endsection @section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Sales Parson</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Sales Parson
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
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Alternative Number</th>
                                    <th>Email</th>
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
                <h5 class="modal-title" id="exampleModalLabel1">Sales Parson Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="full_name" class="form-label">Name</label>
                        <input type="text" id="full_name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="phone" class="form-label">Mobile</label>
                        <input type="text" id="phone" class="form-control" placeholder="Enter Mobile" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" id="email" class="form-control" placeholder="Enter Email" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" id="password" class="form-control" placeholder="Enter Password" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" class="form-control" placeholder="Enter Address" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="dob" class="form-label">DOB</label>
                        <input type="date" id="dob" class="form-control" placeholder="Enter DOB" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="alternative_phone" class="form-label">Alternative Phone</label>
                        <input type="text" id="alternative_phone" class="form-control" placeholder="Enter Alternative Phone" />
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
                <h5 class="modal-title" id="exampleModalLabel1">Sales Parson Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input type="hidden" id="compid">
                        <label for="edit_full_name" class="form-label">Name</label>
                        <input type="text" id="edit_full_name" class="form-control" placeholder="Enter Name" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_phone" class="form-label">Mobile</label>
                        <input type="text" id="edit_phone" class="form-control" placeholder="Enter Mobile" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="text" id="edit_email" class="form-control" placeholder="Enter Email" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_password" class="form-label">Password</label>
                        <input type="text" id="edit_password" class="form-control" placeholder="Enter Password" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <input type="text" id="edit_address" class="form-control" placeholder="Enter Address" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_dob" class="form-label">DOB</label>
                        <input type="date" id="edit_dob" class="form-control" placeholder="Enter DOB" />
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_alternative_phone" class="form-label">Alternative Phone</label>
                        <input type="text" id="edit_alternative_phone" class="form-control" placeholder="Enter Alternative Phone" />
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
        const table = $("#branchTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.salesparsonmanagment.getall') }}",
            },
            columns: [
                {
                    data: "full_name",
                },
                {
                    data: "phone",
                },
                {
                    data: "alternative_phone",
                },
                {
                    data: "email",
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

                        return `${statusButton} ${editButton}`;
                    },
                },

            ],
        });

        // Handle form submission via AJAX
        $('#AddItem').click(function(e) {
            e.preventDefault();

            // Collect form data
            let data = {
                full_name: $('#full_name').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                address : $('#address').val(),
                password : $('#password').val(),
                dob : $('#dob').val(),
                alternative_phone : $('#alternative_phone').val(),
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            };


            // Clear previous validation error messages
            $('.error-text').text('');

            $.ajax({
                url: '{{ route('admin.salesparsonmanagment.store') }}', // Adjust the route as necessary
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
            const url = '{{ route("admin.salesparsonmanagment.get", ":userid") }}'.replace(":userid", userId);
            $.ajax({
                url: url, // Update this URL to match your route
                method: 'GET',
                success: function(data) {
                    // Populate modal fields with the retrieved data
                    $('#compid').val(data.id);
                    $('#edit_full_name').val(data.full_name);
                    $('#edit_phone').val(data.phone);
                    $('#edit_email').val(data.email);
                    $('#edit_address').val(data.address);
                    $('#edit_dob').val(data.dob);
                    $('#edit_alternative_phone').val(data.alternative_phone);

                    // Open the modal
                    $('#editModal').modal('show');
                    setFlash("success", 'Sales Parson found successfully.');
                },
                error: function(xhr) {
                    setFlash("error", "Sales Parson not found. Please try again later.");
                }
            });
        }

        // Handle form submission
        $('#EditComapany').on('click', function() {
            const userId = $('#compid').val(); // Ensure userId is available in the scope
            $.ajax({
                url: '{{ route('admin.salesparsonmanagment.update') }}', // Update this URL to match your route
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    full_name: $('#edit_full_name').val(),
                    email: $('#edit_email').val(),
                    phone: $('#edit_phone').val(),
                    password : $('#edit_password').val(),
                    alternative_phone: $('#edit_alternative_phone').val(),
                    dob: $('#edit_dob').val(),
                    address: $('#edit_address').val(),
                    id: userId // Ensure userId is in scope or adjust accordingly
                },
                success: function(response) {
                    console.log(response.success);
                    if (response.success == true) {
                        // Optionally, refresh the page or update the table with new data
                        //table.ajax.reload();
                        setFlash("success", response.message);
                        $('#editModal').modal('hide'); // Close the modal
                        $('#editModal').find('input, textarea, select').val(''); // Reset form fields
                        table.ajax.reload(); // Reload DataTable
                    } else {
                        for (let field in response.errors) {
                            let $field = $(`#edit_${field}`);
                            if ($field.length) {
                                $field.siblings('.error-text').text(response.errors[field][0]);
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error updating Branch data:', xhr);
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
                        url: "{{ route('admin.salesparsonmanagment.status') }}",
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
                    const url = '{{ route("admin.salesparsonmanagment.destroy", ":userId") }}'.replace(":userId", userId);
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
