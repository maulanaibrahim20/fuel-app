<form id="form-ajax" action="{{ route('user.vehicle.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nama Kendaraan</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Merk</label>
            <input type="text" name="brand" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Model</label>
            <input type="text" name="model" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Tahun</label>
            <input type="number" name="year" class="form-control" min="1900" max="{{ date('Y') }}" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Jenis Bahan Bakar</label>
            <select name="fuel_type" class="form-select" required>
                <option value="">-- pilih --</option>
                <option value="gasoline">Gasoline</option>
                <option value="diesel">Diesel</option>
                <option value="hybrid">Hybrid</option>
                <option value="electric">Electric</option>
                <option value="lpg">LPG</option>
                <option value="pertamax">Pertamax</option>
                <option value="pertamax_plus">Pertamax Plus</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Transmisi</label>
            <select name="transmission" class="form-select" required>
                <option value="">-- pilih --</option>
                <option value="manual">Manual</option>
                <option value="automatic">Automatic</option>
                <option value="cvt">CVT</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nomor Polisi</label>
            <input type="text" name="license_plate" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Warna</label>
            <input type="text" name="color" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Kapasitas Mesin (L)</label>
            <input type="number" step="0.01" name="engine_capacity" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Kapasitas Tangki (L)</label>
            <input type="number" step="0.01" name="tank_capacity" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Odometer Awal (KM)</label>
        <input type="number" step="0.01" name="initial_odometer" class="form-control" value="0">
    </div>

    <div class="mb-3">
        <label class="form-label">Foto Kendaraan</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Catatan</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
        <label class="form-check-label">Aktif</label>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btn-ajax" class="btn btn-primary">
            <i class="bx bx-save"></i> Simpan
        </button>
    </div>
</form>
