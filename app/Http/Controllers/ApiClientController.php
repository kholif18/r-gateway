<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::all();
        return view('api-clients', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => [
                'required',
                'string',
                'max:255',
                'unique:api_clients,client_name',
                'regex:/^[a-zA-Z0-9\s\-\_]+$/', // hanya huruf, angka, spasi, strip, dan underscore
            ],
        ], [
            'client_name.required' => 'Nama aplikasi wajib diisi.',
            'client_name.string' => 'Nama aplikasi harus berupa teks.',
            'client_name.max' => 'Nama aplikasi maksimal 255 karakter.',
            'client_name.unique' => 'Nama aplikasi sudah digunakan. Silakan pilih nama lain.',
            'client_name.regex' => 'Nama aplikasi hanya boleh mengandung huruf, angka, spasi, strip (-), dan underscore (_).',
        ]);

        ApiClient::create([
            'client_name' => $validated['client_name'],
            'api_token' => Str::uuid()->toString(),
            'session_name'  => Auth::user()->username,
            'is_active' => true,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan');
    }

    public function toggle(ApiClient $client)
    {
        $client->update(['is_active' => !$client->is_active]);
        return redirect()->back()->with('success', 'Status client diperbarui.');
    }

    public function regenerate(ApiClient $client)
    {
        $client->update(['api_token' => Str::random(60)]);
        return redirect()->back()->with('success', 'Token baru berhasil dibuat.');
    }

    public function destroy(ApiClient $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }

}
