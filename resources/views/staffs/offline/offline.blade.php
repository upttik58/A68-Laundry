@extends('staffs.main')

@section('title', 'Dashboard')

@section('content')
    <div class="pc-content">
        <div class="row">
            <div class="col-xl-12 col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="d-flex align-items-center">
                                KELOLA ORDERAN OFFLINE
                            </span>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addModalOrderOfflineLaundry">
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orderOfflineLaundryTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>No.HP</th>
                                        <th>Alamat</th>
                                        <th>Jenis Laundry</th>
                                        <th>KG</th>
                                        <th>Total Biaya</th>
                                        <th>Pembayaran</th>
                                        <th>Status</th>
                                        <th>Status Cucian</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModalOrderOfflineLaundry" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="orderOfflineLaundryForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Data Orderan Offline</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama">
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No.HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat">
                        </div>
                        <div class="mb-3">
                            <label for="jenis_laundry" class="form-label">Jenis Laundry</label>
                            <select class="form-select" id="jenis_laundry" name="jenis_laundry">
                                <option disabled selected>Pilih Jenis Laundry</option>
                                @foreach ($jenisLaundry as $jl)
                                    <option value="{{ $jl->id }}">{{ $jl->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="berat" class="form-label">KG</label>
                            <input type="number" step="0.1" class="form-control" id="berat" name="berat">
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Total Biaya</label>
                            <input type="text" class="form-control" id="harga" name="harga" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="pembayaran" class="form-label">Pembayaran</label>
                            <select class="form-select" id="pembayaran" name="pembayaran">
                                <option value="Cash">Tunai</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bayarOrderOffline" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title">Silahkan Lakukan Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="{{ asset('qrcode.png') }}" alt="" srcset="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let data = @json($orderan);

            // Inisialisasi DataTable dan simpan instance-nya
            let table = $('#orderOfflineLaundryTable').DataTable({
                data: data,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'orderan_detail.nama',
                    },
                    {
                        data: 'orderan_detail.no_hp'
                    },
                    {
                        data: 'orderan_detail.alamat'
                    },
                    {
                        data: 'jenis_laundry.nama',
                    },
                    {
                        data: 'berat'
                    },
                    {
                        data: 'harga'
                    },
                    {
                        data: 'pembayaran'
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            let label = data;
                            if (data === 'Belum Lunas') {
                                badgeClass = 'bg-danger';
                            }
                            if (data === 'Sudah Lunas') {
                                badgeClass = 'bg-success';
                            }
                            return `<span class="badge ${badgeClass}">${label}</span>`;
                        }
                    },
                    {
                        data: 'status_cucian',
                        render: function(data, type, row) {
                            let badgeClass = '';
                            let label = data;
                            if (data === 'Orderan Masuk') {
                                badgeClass = 'bg-danger';
                            }
                            if (data === 'Sedang Dicuci') {
                                badgeClass = 'bg-warning';
                            }
                            if (data === 'Sudah Selesai') {
                                badgeClass = 'bg-primary';
                            }
                            if (data === 'Sudah Diambil') {
                                badgeClass = 'bg-success';
                            }
                            return `<span class="badge ${badgeClass}">${label}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            let dropdownItems = '';

                            if (row.status_cucian === 'Orderan Masuk') {
                                if(row.status === 'Belum Lunas'){
                                    if (row.pembayaran === 'Cash') {
                                        dropdownItems += `
                                                <button class="dropdown-item" type="button" onclick="bayar('${row.id}')">Bayar (Cash)</button>
                                            `;
                                    }
    
                                    if (row.pembayaran === 'Transfer') {
                                        dropdownItems += `
                                                <button class="dropdown-item" type="button" onclick="bayarTransfer('${row.snap_token}')">Bayar (Transfer)</button>
                                            `;
                                    }
                                }

                                dropdownItems += `
                                        <button class="dropdown-item" type="button" onclick="editData('${row.id}')">Edit</button>
                                        <button class="dropdown-item" type="button" onclick="hapusData('${row.id}')">Hapus</button>
                                    `;
                            }

                            if (row.status_cucian === 'Sedang Dicuci' && row.status === 'Sudah Lunas') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="cetakNota('${row.id}')">Cetak Nota</button>
                                    <button class="dropdown-item" type="button" onclick="selesai('${row.id}')">Proses Selesai</button>
                                `;
                            }

                            if (row.status_cucian === 'Sudah Selesai') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="cetakNota('${row.id}')">Cetak Nota</button>
                                    <button class="dropdown-item" type="button" onclick="diambil('${row.id}')">Orderan Diambil</button>
                                `;
                            }

                            if (row.status_cucian === 'Sudah Diambil') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="cetakNota('${row.id}')">Cetak Nota</button>
                                `;
                            }

                            return `
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <div class="dropdown-menu">
                                        ${dropdownItems}
                                    </div>
                                </div>
                            `;
                        }
                    }
                ]
            });

            // Simpan instance DataTable ke window agar bisa diakses di editData
            window.orderOfflineLaundryTable = table;
        });

        function editData(id) {
            // Ambil data dari DataTable instance global
            let table = window.orderOfflineLaundryTable;
            let data = table.data().toArray().find(row => String(row.id) === String(id));
            console.log(data);

            if (data) {
                $('#id').val(data.id);
                $('#nama').val(data.orderan_detail.nama);
                $('#no_hp').val(data.orderan_detail.no_hp);
                $('#alamat').val(data.orderan_detail.alamat);
                $('#jenis_laundry').val(data.jenis_laundry.id).trigger('change');
                $('#berat').val(data.berat);
                $('#harga').val(data.harga);
                $('#pembayaran').val(data.pembayaran).trigger('change');
                $('#addModalOrderOfflineLaundry').modal('show');
            }
        }

        function bayarTransfer(id) {
            // SnapToken acquired from previous step
            snap.pay(id, {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    window.location.href = 'offline/bayar/success/' + id;
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                }
            });
        }

        function bayar(id) {
            Swal.fire({
                title: "Konfirmasi Pembayaran",
                text: "Apakah Anda yakin ingin melunasi pembayaran orderan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, bayar!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/offline/bayar/" + id,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    "Berhasil!",
                                    response.message,
                                    "success"
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    "Gagal!",
                                    response.message,
                                    "error"
                                );
                            }
                        },
                        error: function(xhr) {
                            let msg = "Gagal menghapus data. Silakan coba lagi.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                "Gagal!",
                                msg,
                                "error"
                            );
                        }
                    });
                }
            });
        }

        function selesai(id) {
            Swal.fire({
                title: "Konfirmasi Selesai Pesanan",
                text: "Apakah Anda yakin ingin menyelesaikan pesanan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, selesai!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/offline/selesai/" + id,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    "Berhasil!",
                                    response.message,
                                    "success"
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    "Gagal!",
                                    response.message,
                                    "error"
                                );
                            }
                        },
                        error: function(xhr) {
                            let msg = "Gagal menghapus data. Silakan coba lagi.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                "Gagal!",
                                msg,
                                "error"
                            );
                        }
                    });
                }
            });
        }
        
        function diambil(id) {
            Swal.fire({
                title: "Konfirmasi Pesanan Diambil",
                text: "Apakah Anda yakin ingin mengambil pesanan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, diambil!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/offline/diambil/" + id,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    "Berhasil!",
                                    response.message,
                                    "success"
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    "Gagal!",
                                    response.message,
                                    "error"
                                );
                            }
                        },
                        error: function(xhr) {
                            let msg = "Gagal menghapus data. Silakan coba lagi.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                "Gagal!",
                                msg,
                                "error"
                            );
                        }
                    });
                }
            });
        }

        function hapusData(id) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/offline/" + id,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    "Berhasil!",
                                    response.message,
                                    "success"
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    "Gagal!",
                                    response.message,
                                    "error"
                                );
                            }
                        },
                        error: function(xhr) {
                            let msg = "Gagal menghapus data. Silakan coba lagi.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                "Gagal!",
                                msg,
                                "error"
                            );
                        }
                    });
                }
            });
        }

        $('#addModalOrderOfflineLaundry').on('show.bs.modal', function() {
            let id = $('#id').val();
            let form = $('#orderOfflineLaundryForm');
            if (id) {
                form.attr('action', '/offline/edit/' + id);
            } else {
                form.attr('action', '/offline');
            }
        });

        $('#addModalOrderOfflineLaundry').on('hidden.bs.modal', function() {
            $('#orderOfflineLaundryForm').attr('action', '/offline');
            $('#orderOfflineLaundryForm')[0].reset();
            $('#id').val('');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const beratInput = document.getElementById('berat');
            const jenisLaundrySelect = document.getElementById('jenis_laundry');
            const hargaInput = document.getElementById('harga');

            const hargaJenisLaundry = {
                @foreach ($jenisLaundry as $jl)
                    "{{ $jl->id }}": {{ $jl->harga }},
                @endforeach
            };

            function hitungTotalBiaya() {
                const berat = parseFloat(beratInput.value) || 0;
                const jenisId = jenisLaundrySelect.value;
                const hargaPerKg = hargaJenisLaundry[jenisId] || 0;
                hargaInput.value = berat > 0 && hargaPerKg > 0 ? berat * hargaPerKg : '';
            }

            beratInput.addEventListener('input', hitungTotalBiaya);
            jenisLaundrySelect.addEventListener('change', hitungTotalBiaya);
        });
    </script>
@endpush
