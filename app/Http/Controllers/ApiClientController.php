<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SweetAlert2\Laravel\Swal;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::where('user_id', Auth::id())->get();
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
            'user_id'      => Auth::id(), // ⬅️ Simpan ID pembuatnya
            'client_name'  => $request->client_name,
            'api_token'    => Str::uuid()->toString(),
            'session_name' => Auth::user()->username,
            'is_active'    => true,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan.');
    }

    public function toggle(ApiClient $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403); // 🔒 Hindari manipulasi milik user lain
        }

        $client->update(['is_active' => !$client->is_active]);
        return back()->with('success', 'Status client diperbarui.');
    }

    public function regenerate(ApiClient $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        $client->update(['api_token' => Str::uuid()->toString()]);
        return back()->with('success', 'Token baru berhasil dibuat.');
    }

    public function destroy(ApiClient $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        $client->delete();

        Swal::success([
            'title' => 'Berhasil',
            'text' => 'Client berhasil dihapus.',
        ]);

        return redirect()->route('clients.index');
    }
}
