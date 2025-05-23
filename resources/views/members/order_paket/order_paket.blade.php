@extends('members.main')

@section('title', 'Paket Laundry')

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
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="d-flex align-items-center">
                                PAKET LAUNDRY ANDA
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Paket</th>
                                        <th>Jenis Laundry</th>
                                        <th>KG</th>
                                        <th>Terpakai KG</th>
                                        <th>Sisa KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paketLaundry as $pl)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pl->kode_paket }}</td>
                                            <td>{{ $pl->paketLaundry->jenisLaundry->nama }}</td>
                                            <td>{{ $pl->paketLaundry->berat }}</td>
                                            <td>{{ $pl->kg_terpakai }}</td>
                                            <td>{{ $pl->kg_sisa }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="d-flex align-items-center">
                                KELOLA ORDERAN PAKET
                            </span>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addModalOrderPaket">
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orderPaketTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Order</th>
                                        <th>Jenis Laundry</th>
                                        <th>KG</th>
                                        <th>Harga</th>
                                        <th>Jarak</th>
                                        <th>Ongkir</th>
                                        <th>Total Pembayaran</th>
                                        <th>Status Pembayaran</th>
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

    <div class="modal fade" id="addModalOrderPaket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="orderPaketForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Orderan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="jenis_laundry_id" class="form-label">Pilih Paket Laundry Anda</label>
                            <select class="form-select" id="jenis_laundry_id" name="jenis_laundry_id">
                                <option disabled selected>Pilih Paket Laundry</option>
                                @foreach ($paketLaundry as $pl)
                                    <option value="{{ $pl->paketLaundry->jenisLaundry->id }}">{{ $pl->paketLaundry->jenisLaundry->nama }}</option>
                                @endforeach
                            </select>
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

    <div class="modal fade" id="modalSetLocation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Lokasi Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 400px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveLocationBtn">Simpan Lokasi</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let data = @json($orderan);

            // Inisialisasi DataTable dan simpan instance-nya
            let table = $('#orderPaketTable').DataTable({
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
                        data: 'jenis_laundry.nama',
                    },
                    {
                        data: 'berat'
                    },
                    {
                        data: 'harga',
                        render: function(data, type, row) {
                            return 'Rp. ' + parseInt(data).toLocaleString();
                        }
                    },
                    {
                        data: 'order_location.distance',
                        render: function(data, type, row) {
                            return data ? data + ' km' : 'N/A';
                        }
                    },
                    {
                        data: 'order_location.distance',
                        render: function(data, type, row) {
                            return data ? 'Rp. ' + (data * 5000).toLocaleString() : 'N/A';
                        }
                    },
                    {
                        data: 'order_location.distance',
                        render: function(data, type, row) {
                            let totalCost = (data * 5000) + parseInt(row.harga);
                            return 'Rp. ' + totalCost.toLocaleString();
                        }
                    },
                    {
                        data: 'status',
                    },
                    {
                        data: 'status_cucian',
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.status_cucian === 'Orderan Masuk') {
                                return `<div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="#" onclick="editData('${row.id}')">Edit</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="hapusData('${row.id}')">Hapus</a></li>
                                                <li><a class="dropdown-item" href="/orderPaket/setLocation/${row.id}">Set Location</a></li>
                                            </ul>
                                        </div>`;
                            }

                            if (row.status_cucian === 'Menunggu Pembayaran') {
                                return `<div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="#" onclick="bayar('${row.id}')">Bayar</a></li>
                                            </ul>
                                        </div>`;
                            }

                            if (row.status_cucian === 'Cucian Dalam Perjalanan') {
                                return `<div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="#" onclick="selesai('${row.id}')">Orderan Selesai</a></li>
                                            </ul>
                                        </div>`;
                            }
                            return '';
                        }
                    }
                ]
            });

            // Simpan instance DataTable ke window agar bisa diakses di editData
            window.orderPaketTable = table;
        });

        $(document).ready(function() {
            // Your existing JavaScript code here

            // JavaScript for handling the map modal
            $('#modalSetLocation').on('shown.bs.modal', function() {
                // Initialize the map here
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: -34.397,
                        lng: 150.644
                    },
                    zoom: 8
                });

                // Additional map functionality can be added here
            });

            // Save location button click event
            $('#saveLocationBtn').click(function() {
                // Handle saving location logic here
            });
        });

        function editData(id) {
            // Ambil data dari DataTable instance global
            let table = window.orderPaketTable;
            let data = table.data().toArray().find(row => String(row.id) === String(id));
            if (data) {
                $('#id').val(data.id);
                $('#jenis_laundry_id').val(data.jenis_laundry.id).trigger('change');
                $('#berat').val(data.berat);
                $('#harga').val(data.harga);
                $('#addModalOrderPaket').modal('show');
            }
        }

        function setLocation() {
            $('#modalSetLocation').modal('show');
        }

        function selesai(id) {
            Swal.fire({
                title: "Konfirmasi Laundry Selesai",
                text: "Apakah Anda yakin ingin menyelesaikan orderan ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, selesai!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/orderPaket/selesai/" + id,
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
                        url: "/orderPaket/" + id,
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

        function bayar(id) {
            $.ajax({
                url: "/bayarOrderan",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    // SnapToken acquired from previous step
                    snap.pay(response.snap_token, {
                        // Optional
                        onSuccess: function(result) {
                            /* You may add your own js here, this is just example */
                            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                            window.location.href = 'orderPaket/bayar/success/' + response
                                .snap_token;
                        },
                        // Optional
                        onPending: function(result) {
                            /* You may add your own js here, this is just example */
                            document.getElementById('result-json').innerHTML += JSON
                                .stringify(
                                    result, null, 2);
                        },
                        // Optional
                        onError: function(result) {
                            /* You may add your own js here, this is just example */
                            document.getElementById('result-json').innerHTML += JSON
                                .stringify(
                                    result, null, 2);
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire(
                        "Gagal!",
                        "Terjadi kesalahan saat memproses data.",
                        "error"
                    );
                }
            });
        }

        // Ganti action form sesuai id
        $('#addModalOrderPaket').on('show.bs.modal', function(event) {
            let id = $('#id').val();
            let form = $('#orderPaketForm');
            if (id) {
                form.attr('action', '/orderPaket/edit/' + id);
            } else {
                form.attr('action', '/orderPaket');
            }
        });
        // Reset action saat modal ditutup
        $('#addModalOrderPaket').on('hidden.bs.modal', function() {
            $('#orderPaketForm').attr('action', '/orderPaket');
            $('#orderPaketForm')[0].reset();
            $('#id').val('');
        });
    </script>
@endpush
