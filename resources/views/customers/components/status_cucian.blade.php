<div class="overflow-hidden py-7 py-sm-8 py-xl-9 bg-body-tertiary" id="statusCucian">
    <div class="container">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="m-0 text-primary-emphasis text-base leading-7 fw-semibold">
                Cek Status Cucian
            </h2>
            <p class="m-0 mt-2 text-body-emphasis text-4xl tracking-tight fw-bold">
                Pantau Proses Cucian Anda
            </p>
            <p class="m-0 mt-4 text-body text-lg leading-8">
                Masukkan nomor pesanan Anda untuk melihat status cucian
            </p>
            <form id="formStatusCucian" class="mt-4">
                @csrf
                <input type="text" name="nomor_pesanan" id="nomor_pesanan" placeholder="Masukkan nomor pesanan"
                    class="form-control mt-2" required>
                <button type="submit" class="btn btn-lg btn-primary text-white text-sm fw-semibold mt-3">
                    Cek Status
                </button>
            </form>

            <div id="statusTableContainer" class="mt-5 d-none">
                <h3 class="text-primary-emphasis text-lg fw-semibold">List Status Cucian</h3>
                <div class="table-responsive">
                    <table class="table mt-3" id="statusTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Nomor Orderan</th>
                                <th scope="col">Jenis Laundry</th>
                                <th scope="col">Berat</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#formStatusCucian').submit(function(e) {
                e.preventDefault();

                const nomorPesanan = $('#nomor_pesanan').val().trim();
                const token = $('input[name="_token"]').val();

                if (!nomorPesanan) {
                    alert('Silakan masukkan nomor pesanan.');
                    return;
                }

                $.ajax({
                    url: '/cekStatusCucian',
                    method: 'POST',
                    data: {
                        _token: token,
                        nomor_pesanan: nomorPesanan
                    },
                    success: function(response) {
                        console.log(response.data);
                        if (response.status === 'success' && Array.isArray(response.data) &&
                            response.data.length > 0) {
                            let tableBody = '';

                            response.data.forEach(item => {
                                tableBody += `
                                <tr>
                                    <td>${item.nomor_pesanan}</td>
                                    <td>${item.jenis_laundry.nama}</td>
                                    <td>${item.berat}</td>
                                    <td>${item.harga}</td>
                                    <td>${item.status_cucian}</td>
                                    <td>
                                        <form action="" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="_token" value="">
                                            <input type="text" name="contact_number" placeholder="Nomor kontak"
                                                class="form-control me-2" style="width: 300px;" required>
                                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                            });

                            $('#statusTable tbody').html(tableBody);
                            $('#statusTableContainer').removeClass('d-none');
                        } else {
                            alert('Pesanan tidak ditemukan.');
                            $('#statusTableContainer').addClass('d-none');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan pada server.');
                        $('#statusTableContainer').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
