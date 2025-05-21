@extends('owners.main')

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
                                KELOLA PAKET LAUNDRY
                            </span>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addModalPaketLaundry">
                                Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="paketLaundryTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis</th>
                                        <th>Total KG</th>
                                        <th>Harga</th>
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
    <div class="modal fade" id="addModalPaketLaundry" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="paketLaundryForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Data Paket Laundry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="jenis_laundry_id" class="form-label">Jenis</label>
                            <select class="form-select" id="jenis_laundry_id" name="jenis_laundry_id">
                                <option disabled selected>Pilih Jenis Laundry</option>
                                @foreach ($jenisLaundry as $jl)
                                    <option value="{{ $jl->id }}">{{ $jl->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="berat" class="form-label">Total KG</label>
                            <input type="text" class="form-control" id="berat" name="berat">
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga">
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
            let data = @json($paketLaundry);

            // Inisialisasi DataTable dan simpan instance-nya
            let table = $('#paketLaundryTable').DataTable({
                data: data,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'jenis_laundry.nama',
                        defaultContent: '-'
                    },
                    {
                        data: 'berat'
                    },
                    {
                        data: 'harga'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `<button type="button" class="btn btn-sm btn-info" onclick="editData('${row.id}')">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusData('${row.id}')">Hapus</button>`;
                        }
                    }
                ]
            });

            // Simpan instance DataTable ke window agar bisa diakses di editData
            window.paketLaundryTable = table;
        });

        function editData(id) {
            // Ambil data dari DataTable instance global
            let table = window.paketLaundryTable;
            let data = table.data().toArray().find(row => String(row.id) === String(id));
            if (data) {
                $('#jenis_laundry_id').val(data.jenis_laundry_id).trigger('change');
                $('#harga').val(data.harga);
                $('#berat').val(data.berat);
                $('#id').val(data.id);
                $('#addModalPaketLaundry').modal('show');
            }
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
                        url: "/paketLaundry/" + id,
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

        // Ganti action form sesuai id
        $('#addModalPaketLaundry').on('show.bs.modal', function(event) {
            let id = $('#id').val();
            let form = $('#paketLaundryForm');
            if (id) {
                form.attr('action', '/paketLaundry/edit/' + id);
            } else {
                form.attr('action', '/paketLaundry');
            }
        });
        // Reset action saat modal ditutup
        $('#addModalPaketLaundry').on('hidden.bs.modal', function() {
            $('#paketLaundryForm').attr('action', '/paketLaundry');
            $('#paketLaundryForm')[0].reset();
            $('#id').val('');
        });
    </script>
@endpush
