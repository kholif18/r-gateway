@extends('components.app')

@section('title', 'Daftar API Clients')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar API Clients</div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>API Token</th>
                        <th>Status</th>
                        <th>Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->client_name }}</td>
                            <td><code>{{ $client->api_token }}</code></td>
                            <td>
                                @if($client->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $client->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Belum ada client API</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
