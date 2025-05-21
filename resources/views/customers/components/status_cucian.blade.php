<div class="overflow-hidden py-7 py-sm-8 py-xl-9 bg-body-tertiary" id="statusCucian">
    <div class="container">
        <div>
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
                <form action="" method="POST" class="mt-4">
                    @csrf
                    <input type="text" name="order_number" placeholder="Masukkan nomor pesanan"
                        class="form-control mt-2">
                    <button type="submit" class="btn btn-lg btn-primary text-white text-sm fw-semibold mt-3">
                        Cek Status
                    </button>
                </form>
            </div>
            <h3 class="text-primary-emphasis text-lg fw-semibold">List Status Cucian</h3>
            <div class="table-responsive">
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th scope="col">Pesanan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pesanan 123</td>
                            <td>Sedang Dicuci</td>
                            <td>
                                <form action="" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <input type="text" name="contact_number" placeholder="Nomor kontak"
                                        class="form-control me-2" style="width: 300px;">
                                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
