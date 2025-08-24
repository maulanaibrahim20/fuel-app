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
                <h4 class="mg-b-0 tx-spacing--1">Vehicles</h4>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-modal-url="{{ route('user.vehicle.create') }}"
                    data-modal-title="Add Vehicleser" data-modal-size="modal-lg">
                    <i class="bx bx-plus"></i> Add Vehicle
                </button>
            </div>
        </div>

        <!-- Vehicle List -->
        <div class="row">
            @forelse ($vehicles as $vehicle)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="row g-0 h-100">
                            <div class="col-md-4 d-flex align-items-center">
                                <img class="card-img p-2 rounded"
                                    src="{{ $vehicle->image ? asset('storage/' . $vehicle->image) : url('/assets/img/default-car.png') }}"
                                    alt="{{ $vehicle->name }}" />
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-1">{{ $vehicle->name }}</h5>
                                    <p class="card-text text-muted mb-1">
                                        <i class="fas fa-car-side"></i> {{ ucfirst($vehicle->brand) }} {{ $vehicle->model }}
                                    </p>
                                    <p class="card-text text-muted mb-1">
                                        <i class="fas fa-gas-pump"></i> {{ ucfirst($vehicle->fuel_type) }} |
                                        <i class="fas fa-cogs"></i> {{ strtoupper($vehicle->transmission) }}
                                    </p>
                                    <p class="card-text text-muted mb-1">
                                        <i class="fas fa-id-card"></i> {{ $vehicle->license_plate }}
                                    </p>
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-palette"></i> {{ $vehicle->color ?? '-' }}
                                    </p>
                                    <div class="mt-auto d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary open-modal"
                                            data-modal-url="{{ route('user.vehicle.edit', $vehicle->id) }}"
                                            data-modal-title="Edit Vehicle">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ url('user.vehicle.destroy', $vehicle->id) }}" method="POST"
                                            class="d-inline-block form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No vehicles have been added yet.</div>
                </div>
            @endforelse
            <div class="d-flex justify-content-center mt-4">
                @if ($vehicles->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination">

                            {{-- First Page Link --}}
                            <li class="page-item {{ $vehicles->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $vehicles->url(1) }}">
                                    <i class="ti ti-chevrons-left ti-xs"></i>
                                </a>
                            </li>

                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $vehicles->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $vehicles->previousPageUrl() ?? '#' }}">
                                    <i class="ti ti-chevron-left ti-xs"></i>
                                </a>
                            </li>

                            {{-- Pagination Elements --}}
                            @foreach ($vehicles->links()->elements[0] ?? [] as $page => $url)
                                <li class="page-item {{ $page == $vehicles->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            {{-- Next Page Link --}}
                            <li class="page-item {{ !$vehicles->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $vehicles->nextPageUrl() ?? '#' }}">
                                    <i class="ti ti-chevron-right ti-xs"></i>
                                </a>
                            </li>

                            {{-- Last Page Link --}}
                            <li class="page-item {{ !$vehicles->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $vehicles->url($vehicles->lastPage()) }}">
                                    <i class="ti ti-chevrons-right ti-xs"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // konfirmasi delete dengan SweetAlert
        $(document).on("submit", ".form-delete", function(e) {
            e.preventDefault();
            let form = this;

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data kendaraan akan hilang permanen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
