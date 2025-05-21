<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginAdmin(){
        return view('admins.login');
    }

    public function loginCustomer(){
        return view('customers.register');
    }
}
