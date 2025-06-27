<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::all();
        return view('api-clients', compact('clients'));
    }
}
