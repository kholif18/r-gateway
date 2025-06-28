@extends('components.app')

@section('title', 'List of API Clients')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">List of API Clients</div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClientModal">
                <i class="fas fa-plus me-1"></i> Add Client
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>API Token</th>
                        <th>Status</th>
                        <th>Create</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->client_name }}</td>
                            <td>
                                <code>{{ $client->api_token }}</code>
                                <i class="fas fa-copy ms-2 copy-token" data-token="{{ $client->api_token }}"></i>
                            </td>
                            <td>
                                @if($client->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Non-active</span>
                                @endif
                            </td>
                            <td>{{ $client->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                {{-- Toggle Status --}}
                                <form action="{{ route('clients.toggle', $client->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $client->is_active ? 'btn-danger' : 'btn-success' }}" title="{{ $client->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="fas {{ $client->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>

                                {{-- Regenerate Token --}}
                                <form action="{{ route('clients.regenerate', $client->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success" title="Regenerate Token">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Delete API">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">There is no client API yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Add Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addClientForm" action="{{ route('clients.store') }}" method="POST">
                @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="clientName" class="form-label">Application Name</label>
                            <input type="text" class="form-control" id="clientName" name="client_name" placeholder="Enter application name" required autofocus>
                            <div class="form-text">Example: Mobile App, Web Dashboard, dll.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="feedbackToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div id="toastHeader" class="toast-header">
                <strong class="me-auto"  id="toastTitle"></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody"></div>
        </div>
    </div>

    <script>
        // Autofocus modal
        document.addEventListener('DOMContentLoaded', function () {
            const addClientModal = document.getElementById('addClientModal');
            const clientNameInput = document.getElementById('clientName');

            addClientModal.addEventListener('shown.bs.modal', function () {
                clientNameInput.focus();
            });
        });
        // Inisialisasi tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                const toastEl = document.getElementById('feedbackToast');
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

                // Set konten dan warna header
                document.getElementById('toastTitle').textContent = 'Sukses';
                document.getElementById('toastHeader').classList.add('bg-success', 'text-white');
                document.getElementById('toastBody').textContent = @json(session('success'));

                toast.show();
            @elseif (session('error'))
                const toastEl = document.getElementById('feedbackToast');
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });

                document.getElementById('toastTitle').textContent = 'Gagal';
                document.getElementById('toastHeader').classList.add('bg-danger', 'text-white');
                document.getElementById('toastBody').textContent = @json(session('error'));

                toast.show();
            @endif
        });
        
        document.addEventListener('DOMContentLoaded', function () {
            const copyButtons = document.querySelectorAll('.copy-token');

            copyButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const token = this.getAttribute('data-token');

                    // Salin ke clipboard
                    navigator.clipboard.writeText(token).then(() => {
                        // Ubah ikon jadi centang untuk memberi feedback visual
                        const originalClass = this.className;
                        this.classList.remove('fa-copy');
                        this.classList.add('fa-check');

                        // Kembalikan lagi ke ikon copy setelah 1.5 detik
                        setTimeout(() => {
                            this.classList.remove('fa-check');
                            this.classList.add('fa-copy');
                        }, 1500);
                    }).catch(err => {
                        console.error('Gagal menyalin token:', err);
                    });
                });
            });
        });
    </script>
@endsection
