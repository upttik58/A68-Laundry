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
                                KELOLA ORDERAN LANGSUNG
                            </span>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addModalOrderLangsung">
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orderLangsungTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Order</th>
                                        <th>Jenis Laundry</th>
                                        <th>KG</th>
                                        <th>Harga</th>
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

    <div class="modal fade" id="addModalOrderLangsung" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="orderLangsungForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Orderan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="jenis_laundry_id" class="form-label">Jenis Laundry</label>
                            <select class="form-select" id="jenis_laundry_id" name="jenis_laundry_id">
                                <option disabled selected>Pilih Jenis Laundry</option>
                                @foreach ($jenisLaundry as $jl)
                                    <option value="{{ $jl->id }}">{{ $jl->nama }} - Rp.
                                        {{ $jl->harga }}/{{ $jl->berat }} KG</option>
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
            let table = $('#orderLangsungTable').DataTable({
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
                            if (row.status === 'Belum Lunas') {
                                return `<button type="button" class="btn btn-sm btn-info" onclick="editData('${row.id}')">Edit</button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusData('${row.id}')">Hapus</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="bayar('${row.snap_token}')">Bayar</button>`;
                            } else {
                                return '';
                            }
                        }
                    }
                ]
            });

            // Simpan instance DataTable ke window agar bisa diakses di editData
            window.orderLangsungTable = table;
        });

        // function editData(id) {
        //     // Ambil data dari DataTable instance global
        //     let table = window.orderLangsungTable;
        //     let data = table.data().toArray().find(row => String(row.id) === String(id));
        //     if (data) {
        //         $('#id').val(data.id);
        //         $('#paket_laundry_id').val(data.paket_laundry_id).trigger('change');
        //         $('#addModalOrderLangsung').modal('show');
        //     }
        // }

        // function hapusData(id) {
        //     Swal.fire({
        //         title: "Apakah Anda yakin?",
        //         text: "Data yang dihapus tidak dapat dikembalikan!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Ya, hapus!",
        //         cancelButtonText: "Batal"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "/orderLangsungTable/" + id,
        //                 type: "POST",
        //                 headers: {
        //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        //                 },
        //                 data: {
        //                     _token: "{{ csrf_token() }}"
        //                 },
        //                 success: function(response) {
        //                     if (response.success) {
        //                         Swal.fire(
        //                             "Berhasil!",
        //                             response.message,
        //                             "success"
        //                         ).then(() => {
        //                             location.reload();
        //                         });
        //                     } else {
        //                         Swal.fire(
        //                             "Gagal!",
        //                             response.message,
        //                             "error"
        //                         );
        //                     }
        //                 },
        //                 error: function(xhr) {
        //                     let msg = "Gagal menghapus data. Silakan coba lagi.";
        //                     if (xhr.responseJSON && xhr.responseJSON.message) {
        //                         msg = xhr.responseJSON.message;
        //                     }
        //                     Swal.fire(
        //                         "Gagal!",
        //                         msg,
        //                         "error"
        //                     );
        //                 }
        //             });
        //         }
        //     });
        // }

        // function bayar(id) {
        //     // SnapToken acquired from previous step
        //     snap.pay(id, {
        //         // Optional
        //         onSuccess: function(result) {
        //             /* You may add your own js here, this is just example */
        //             // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
        //             window.location.href = 'orderLangsungTable/bayar/success/' + id;
        //         },
        //         // Optional
        //         onPending: function(result) {
        //             /* You may add your own js here, this is just example */
        //             document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
        //         },
        //         // Optional
        //         onError: function(result) {
        //             /* You may add your own js here, this is just example */
        //             document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
        //         }
        //     });
        // }

        document.addEventListener('DOMContentLoaded', function() {
            const beratInput = document.getElementById('berat');
            const jenisLaundrySelect = document.getElementById('jenis_laundry_id');
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

        // // Ganti action form sesuai id
        // $('#addModalOrderLangsung').on('show.bs.modal', function(event) {
        //     let id = $('#id').val();
        //     let form = $('#orderLangsungForm');
        //     if (id) {
        //         form.attr('action', '/orderLangsungTable/edit/' + id);
        //     } else {
        //         form.attr('action', '/orderLangsungTable');
        //     }
        // });
        // // Reset action saat modal ditutup
        // $('#addModalOrderLangsung').on('hidden.bs.modal', function() {
        //     $('#orderLangsungForm').attr('action', '/orderLangsungTable');
        //     $('#orderLangsungForm')[0].reset();
        //     $('#id').val('');
        // });
    </script>
@endpush
