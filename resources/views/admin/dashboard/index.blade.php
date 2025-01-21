@extends('admin.layouts.app')
@section('style')
@endsection  

@section('content')

<!-- Content -->

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-3 mb-2 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-12">
                        <div class="card-body text-center">
                            <h5 class="fw-medium d-block mb-1">Total Customer</h5>
                            <p class="card-title mb-2">Active - {{ $customerActiveCount }} || In-Active - {{ $customerInactiveCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Total Bills</h5>
                                    <p class="card-title mb-2">Total - {{ $totalBill }}</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Today Bills</h5>
                                    <p class="card-title mb-2">Total - {{ $todayCount }}</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Month Bills</h5>
                                    <p class="card-title mb-2">Total - {{ $currentMonthCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-2 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-12">
                        <div class="card-body text-center">
                            <h5 class="fw-medium d-block mb-1">Total User</h5>
                            <p class="card-title mb-2">Active - {{ $totalUserActive }} || In-Active - {{ $totalUserInactive }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Total Collection</h5>
                                    <p class="card-title mb-2">Total - 10</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Today Collection</h5>
                                    <p class="card-title mb-2">Total - 10</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5 class="fw-medium d-block mb-1">Month Collection</h5>
                                    <p class="card-title mb-2">Total - 10</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fw-medium d-block mb-1">Total Sales Person</h5>
                            <p class="card-title mb-2">Active - {{ $salesparsonActiveCount }} || In-Active - {{ $salesparsonInactiveCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fw-medium d-block mb-1">Total Approved Receipt</h5>
                            <p class="card-title mb-2">Total - 10</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="fw-medium d-block mb-1">Total Unapproved Receipt</h5>
                            <p class="card-title mb-2">Total - 10</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->


@endsection

@section('script')
@endsection