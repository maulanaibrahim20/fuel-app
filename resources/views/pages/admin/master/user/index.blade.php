@extends('layouts.admin.main')
@section('content')
    <div class="container pd-x-0">
        <!-- Breadcrumb -->
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Fuel Records</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Fuel Records Management</h4>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-header pd-t-20 pd-b-15">
                <h6 class="mg-b-0">Filters & Search</h6>
            </div>
            <div class="card-body pd-20">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mg-b-0">
                                <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Vehicle</label>
                                <select class="form-control select2" name="vehicle_id">
                                    <option value="">All Vehicles</option>
                                    <option value="1">Honda Civic 2020</option>
                                    <option value="2">Toyota Avanza 2019</option>
                                    <option value="3">Yamaha NMAX 2021</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mg-b-0">
                                <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Date
                                    From</label>
                                <input type="date" class="form-control" name="date_from" value="2024-01-01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mg-b-0">
                                <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Date To</label>
                                <input type="date" class="form-control" name="date_to" value="2024-12-31">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mg-b-0">
                                <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search records..."
                                        name="search">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="applyFilter()">
                                            <i data-feather="search" class="wd-12"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table id="usersTable" class="table table-dashboard mg-b-0 datatable mt-1"
                    data-ajax="{{ route('admin.user.getData') }}" data-server-side="true" data-processing="true"
                    data-ordering="true" data-length-menu="[10,25,50,100]" data-state-save="true">
                    <thead>
                        <tr>
                            <th data="id" orderable="false" searchable="false">ID</th>
                            <th data="name">Name</th>
                            <th data="email">Email</th>
                            <th data="action" orderable="false" searchable="false" class-name="text-center">Action</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
@endsection
