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
                                LIST PAKET LAUNDRY
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                         <div class="table-responsive">
                            <table id="orderLangsungTable" class="table table-striped">
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
                                    @foreach ($paketLaundry as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode_paket }}</td>
                                            <td>{{ $item->paketLaundry->jenisLaundry->nama }}</td>
                                            <td>{{ $item->paketLaundry->berat }}</td>
                                            <td>{{ $item->terpakai_kg }}</td>
                                            <td>{{ $item->paketLaundry->berat - 10 }}</td>
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
                            <table id="orderLangsungTable" class="table table-striped">
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
                <form id="orderLangsungForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Orderan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="paket_laundry_id" class="form-label">Paket Laundry</label>
                            <select class="form-select" id="paket_laundry_id" name="paket_laundry_id">
                                <option disabled selected>Pilih Paket Laundry</option>
                                @foreach ($paketLaundry as $pl)
                                    <option value="{{ $pl->id }}">{{ $pl->kode_paket }}</option>
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
@endsection