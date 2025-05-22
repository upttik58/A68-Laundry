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
                                KELOLA ORDERAN ONLINE
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orderOnlineLaundryTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Orderan</th>
                                        <th>Nama</th>
                                        <th>No.HP</th>
                                        <th>Alamat</th>
                                        <th>Jenis Laundry</th>
                                        <th>Ongkir/Jarak</th>
                                        <th>Gmaps</th>
                                        <th>KG</th>
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
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

    <div class="modal fade" id="modalInputTimbangan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/online/berat" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Masukkan Timbangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="jenisLaundryId" id="jenisLaundryId">
                        <div class="mb-3">
                            <label for="jenis_laundry" class="form-label">Jenis Laundry</label>
                            <input type="text" class="form-control" id="jenis_laundry" name="jenis_laundry" disabled readonly>
                        </div>
                        <div class="mb-3">
                            <label for="berat" class="form-label">Berat</label>
                            <input type="text" class="form-control" id="berat" name="berat">
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" class="form-control" id="harga" name="harga">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
            let table = $('#orderOnlineLaundryTable').DataTable({
                data: data,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'nomor_pesanan',
                    },
                    {
                        data: 'user.nama'
                    },
                    {
                        data: 'user.no_hp'
                    },
                    {
                        data: 'user.alamat',
                    },
                    {
                        data: 'jenis_laundry.nama',
                    },
                    {
                        data: 'order_location.distance',
                        render: function(data, type, row) {
                            return 'Rp ' + (data * 5000).toLocaleString() + ' / ' + data + ' km';
                        }
                    },
                    {
                        data: 'order_location.latitude',
                        render: function(data, type, row) {
                            return `<a href="https://www.google.com/maps/search/?api=1&query=${row.order_location.latitude},${row.order_location.longitude}" target="_blank">Gmaps</a>`;
                        }
                    },
                    {
                        data: 'berat',
                        defaultContent: '-',
                    },
                    {
                        data: 'status_cucian',
                        defaultContent: '-',
                    },
                    {
                        data: 'status',
                        defaultContent: '-',
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            let dropdownItems = '';
                            if (row.status_cucian === 'Menunggu Dijemput') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="jemputOrderan('${row.id}')">Jemput Orderan</button>
                                `;
                            }

                            if (row.status_cucian === 'Menunggu Timbangan') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="inputTimbangan('${row.id}')">Masukkan Berat</button>
                                `;
                            }

                            if (row.status_cucian === 'Sedang Dicuci' && row.status === 'Sudah Lunas') {
                                dropdownItems += `
                                    <button class="dropdown-item" type="button" onclick="antarCucian('${row.id}')">Antar Cucian</button>
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
            window.orderOnlineLaundryTable = table;
        });

        function inputTimbangan(id) {
            // Ambil data dari DataTable instance global
            let table = window.orderOnlineLaundryTable;
            let data = table.data().toArray().find(row => String(row.id) === String(id));
            if (data) {
                $('#id').val(data.id);
                $('#jenis_laundry').val(data.jenis_laundry.nama);
                $('#jenisLaundryId').val(data.jenis_laundry.id);
                $('#modalInputTimbangan').modal('show');
            }
        }

        function jemputOrderan(id) {
            Swal.fire({
                title: "Konfirmasi Jemput Pesanan",
                text: "Apakah Anda yakin ingin menjemput pesanan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, jemput!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/online/jemput/" + id,
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
        
        function antarCucian(id) {
            Swal.fire({
                title: "Konfirmasi Antar Cucian",
                text: "Apakah Anda yakin ingin mengantar cucian ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, antar!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/online/antar/" + id,
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

        document.addEventListener('DOMContentLoaded', function() {
            const beratInput = document.getElementById('berat');
            const jenisLaundrySelect = document.getElementById('jenisLaundryId');
            console.log(jenisLaundrySelect);
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
        });
    </script>
@endpush
