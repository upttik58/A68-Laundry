@extends('members.main')

@section('title', 'Paket Laundry')

@section('content')
    <div class="pc-content">
        <div class="row">
            <div class="col-xl-12 col-md-12">
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
                                        <th>Nama</th>
                                        <th>Nomor Telepon</th>
                                        <th>No.HP</th>
                                        <th>Alamat</th>
                                        <th>Status</th>
                                        <th>KG</th>
                                        <th>Sisa Paket KG</th>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#orderOnlineLaundryTable').DataTable({
                processing: true,
                serverSide: false,
                data: [{
                        "DT_RowIndex": 1,
                        "name": "John Doe",
                        "phone_number": "081234567890",
                        "alt_phone_number": "081298765432",
                        "address": "Jl. Mawar No. 1",
                        "status": "Menunggu Pembayaran",
                        "weight": "5 KG",
                        "remaining_kg": "15 KG"
                    },
                    {
                        "DT_RowIndex": 2,
                        "name": "Jane Smith",
                        "phone_number": "081234567891",
                        "alt_phone_number": "081234567892",
                        "address": "Jl. Melati No. 2",
                        "status": "Diproses",
                        "weight": "3 KG",
                        "remaining_kg": "7 KG"
                    }
                ],
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'phone_number'
                    },
                    {
                        data: 'alt_phone_number'
                    },
                    {
                        data: 'address'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'weight'
                    },
                    {
                        data: 'remaining_kg'
                    },
                    {
                        data: 'name',
                        render: function(data) {
                            return `
                        <button class='btn btn-sm btn-info' onclick='jemputOrderan("${data}")'>Jemput Orderan</button>
                        <button class='btn btn-sm btn-primary' onclick='masukkanTimbangan("${data}")'>Masukkan Timbangan</button>
                    `;
                        }
                    }
                ]
            });
        });

        // Function to Jemput Orderan
        function jemputOrderan(name) {
            let table = $('#orderOnlineLaundryTable').DataTable();
            let data = table.data().toArray().find(row => row.name === name);

            if (data) {
                Swal.fire({
                    title: "Jemput Orderan",
                    text: `Apakah Anda yakin ingin menjemput orderan ${data.name}?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya, jemput!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        data.status = "Dijemput";
                        table.row().invalidate().draw();
                        Swal.fire("Berhasil!", "Orderan dijemput.", "success");
                    }
                });
            }
        }

        // Function to Masukkan Timbangan
        function masukkanTimbangan(name) {
            let table = $('#orderOnlineLaundryTable').DataTable();
            let data = table.data().toArray().find(row => row.name === name);

            if (data) {
                Swal.fire({
                    title: "Masukkan Timbangan",
                    input: 'number',
                    inputLabel: 'Masukkan Berat (KG)',
                    inputValue: data.weight ? parseFloat(data.weight) : '',
                    showCancelButton: true,
                    confirmButtonText: "Simpan",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let beratBaru = result.value;
                        if (beratBaru > 0) {
                            data.weight = `${beratBaru} KG`;
                            data.remaining_kg = `${parseFloat(data.remaining_kg) - beratBaru} KG`;
                            table.row().invalidate().draw();
                            Swal.fire("Berhasil!", "Data timbang berhasil disimpan.", "success");
                        } else {
                            Swal.fire("Gagal!", "Berat harus lebih dari 0.", "error");
                        }
                    }
                });
            }
        }
    </script>
@endpush
