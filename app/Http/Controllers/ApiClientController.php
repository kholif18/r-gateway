<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::all();
        return view('api-clients', compact('clients'));
    }

    public function store(Request $request)
    {
        // Normalisasi nama client
        $request->merge([
            'client_name' => Str::of($request->client_name)
                ->replace(' ', '_')
                ->lower()
                ->toString(),
        ]);

        $validator = Validator::make($request->all(), [
            'client_name' => [
                'required',
                'string',
                'max:255',
                'unique:api_clients,client_name',
                'regex:/^[a-z0-9\-_]+$/',
            ],
        ], [
            'client_name.required' => 'Nama aplikasi wajib diisi.',
            'client_name.string' => 'Nama aplikasi harus berupa teks.',
            'client_name.max' => 'Nama aplikasi maksimal 255 karakter.',
            'client_name.unique' => 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.',
            'client_name.regex' => 'Nama aplikasi hanya boleh huruf kecil, angka, strip (-), dan underscore (_), tanpa spasi.',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->with('error', $validator->errors()->first());
        }

        ApiClient::create([
            'client_name'   => $request->client_name,
            'api_token'     => Str::uuid()->toString(),
            'session_name'  => Auth::user()->username,
            'is_active'     => true,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan.');
    }

    public function toggle(ApiClient $client)
    {
        $client->update(['is_active' => !$client->is_active]);
        return back()->with('success', 'Status client diperbarui.');
    }

    public function regenerate(ApiClient $client)
    {
        $client->update(['api_token' => Str::uuid()->toString()]);
        return back()->with('success', 'Token baru berhasil dibuat.');
    }

    public function destroy(ApiClient $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client berhasil dihapus.');
    }
}
