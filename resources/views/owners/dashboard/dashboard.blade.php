@extends('owners.main')

@section('title', 'Dashboard')

@section('content')
    <div class="pc-content">
        <div class="row">
            <!-- CARD REGIS & TIDAK REGIS -->
            <div class="col-xl-4 col-md-4">
                <div class="card bg-green-900 dashnum-card text-white overflow-hidden">
                    <span class="round small"></span>
                    <span class="round big"></span>
                    <div class="card-body">
                        <div class="avtar avtar-lg"><i class="text-white ti ti-receipt"></i></div>
                        <span class="text-white d-block f-34 f-w-500 my-2">
                            {{$totalPendapatan}} <i class="ti ti-arrow-up-right-circle opacity-50"></i>
                        </span>
                        <p class="mb-0 opacity-50">Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4">
                <div class="card bg-blue-900 dashnum-card text-white overflow-hidden">
                    <span class="round small"></span>
                    <span class="round big"></span>
                    <div class="card-body">
                        <div class="avtar avtar-lg"><i class="text-white ti ti-receipt"></i></div>
                        <span class="text-white d-block f-34 f-w-500 my-2">
                            {{$totalOrderan}} <i class="ti ti-arrow-up-right-circle opacity-50"></i>
                        </span>
                        <p class="mb-0 opacity-50">Total Orderan</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4">
                <div class="card bg-red-900 dashnum-card text-white overflow-hidden">
                    <span class="round small"></span>
                    <span class="round big"></span>
                    <div class="card-body">
                        <div class="avtar avtar-lg"><i class="text-white ti ti-receipt"></i></div>
                        <span class="text-white d-block f-34 f-w-500 my-2">
                            {{$totalMember}} <i class="ti ti-arrow-up-right-circle opacity-50"></i>
                        </span>
                        <p class="mb-0 opacity-50">Total Member</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 100%;
            height: 100%;
        }

        .overlay-backdrop {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        .overlay-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
    </style>
@endpush