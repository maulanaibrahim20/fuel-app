@extends('layouts.admin.main')
@section('content')
    <div class="container pd-x-0">
        <!-- Header & Breadcrumb -->
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Receipt</h4>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-modal-url="{{ route('user.vehicle.create') }}"
                    data-modal-title="Add Vehicleser" data-modal-size="modal-lg">
                    <i class="bx bx-plus"></i> Add Receipt Manual
                </button>

                <a class="btn btn-warning" onclick="openCamera()">
                    <i class="bx bx-camera"></i> Add Receipt Scan
                </a>

                <form id="scan-form" action="{{ route('user.receipt.analyze') }}" method="POST"
                    enctype="multipart/form-data" style="display:none;">
                    @csrf
                    <input type="file" name="receipt" accept="image/*" capture="environment" id="cameraInput"
                        onchange="document.getElementById('scan-form').submit();">
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function openCamera() {
            document.getElementById('cameraInput').click();
        }
    </script>
@endpush
