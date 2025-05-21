<!doctype html>
<html lang="en">

<head>
    <title>Login | E-Laundry A68</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="icon" href="{{ asset('assets_customer/logo/logo.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" />
    <link rel="stylesheet" href="{{ asset('assets_admin/css/style.css') }}" id="main-style-link" />

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 12px;
            width: 40%;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .close:hover {
            color: red;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab {
            overflow: hidden;
            border-bottom: 2px solid #ddd;
        }

        .tab button {
            background-color: transparent;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 10px 15px;
            transition: 0.3s;
            font-size: 16px;
        }

        .tab button:hover {
            background-color: #ddd;
        }

        .tab button.active {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .tabcontent {
            padding: 15px;
            display: none;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                margin: 20% auto;
                padding: 15px;
            }

            .auth-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 10px;
            }

            .auth-form {
                width: 100%;
                padding: 15px;
            }

            .card {
                margin: 0 10px;
                padding: 15px;
            }

            .form-floating input {
                font-size: 14px;
                padding: 10px;
            }

            .tab button {
                font-size: 14px;
                padding: 8px 10px;
            }

            .tabcontent {
                font-size: 14px;
            }
        }

        .accordion-container {
            display: none;
        }

        .accordion {
            background-color: #f1f1f1;
            cursor: pointer;
            padding: 10px;
            border: none;
            text-align: left;
            width: 100%;
        }

        .accordion-content {
            display: none;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            #tabMenu {
                display: none;
            }

            .accordion-container {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <a href="#" class="d-flex justify-content-center">
                            <img src="{{ asset('assets_customer/logo/logo.png') }}" alt="image" class="img-fluid brand-logo"
                                width="130" />
                        </a>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <div class="auth-header text-center">
                                    <h2 class="text-secondary mt-5"><b>Hi, Welcome Back To E-Laundry A68</b></h2>
                                    <p class="f-16 mt-2">Enter your credentials to continue</p>
                                </div>
                            </div>
                        </div>
                        <form action="/login" method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="Username" />
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Password" />
                                <label for="password">Password</label>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @if ($errors->count() > 1)
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $errors->first() }}
                                    @endif
                                </div>
                            @endif
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-secondary">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
