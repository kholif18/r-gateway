@extends('components.app')

@section('title', 'Daftar API Clients')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar API Clients</div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClientModal">
                <i class="fas fa-plus me-1"></i> Tambah Client
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>API Token</th>
                        <th>Status</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->client_name }}</td>
                            <td>
                                <code>{{ $client->api_token }}</code>
                                <i class="fas fa-copy copy-token ms-2" data-token="{{ $client->api_token }}"></i>
                            </td>
                            <td>
                                @if($client->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $client->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                {{-- Toggle Status --}}
                                <form action="{{ route('clients.toggle', $client->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $client->is_active ? 'btn-danger' : 'btn-success' }}" title="{{ $client->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
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
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus client ini?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus API">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
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

    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Tambah Aplikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addClientForm" action="{{ route('clients.store') }}" method="POST">
                @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="clientName" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="clientName" name="client_name" placeholder="Masukkan nama aplikasi" required>
                            <div class="form-text">Contoh: Mobile App, Web Dashboard, dll.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto"><i class="fas fa-check-circle me-2"></i> Sukses</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Client baru berhasil ditambahkan!
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi tooltip
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Inisialisasi toast
        const toastEl = document.getElementById('successToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        
        // Form submission
        // document.getElementById('addClientForm').addEventListener('submit', function(e) {
        //     e.preventDefault();

        //     // Simulasi penambahan client
        //     const clientName = document.getElementById('clientName').value;
        //     console.log(`Client baru ditambahkan: ${clientName}`);

        //     // Tampilkan toast sukses
        //     toast.show();

        //     // Reset form dan tutup modal
        //     this.reset();
        //     const modal = bootstrap.Modal.getInstance(document.getElementById('addClientModal'));
        //     modal.hide();
        // });
        
        // Fungsi untuk menyalin token API ke clipboard
function setupTokenCopy() {
    // Inisialisasi tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Inisialisasi toast
    const toastEl = document.getElementById('copyToast');
    const toast = toastEl ? new bootstrap.Toast(toastEl, { delay: 2000 }) : null;
    
    // Fungsi untuk menyalin token
    document.querySelectorAll('.copy-token').forEach(icon => {
        icon.addEventListener('click', function() {
            const token = this.getAttribute('data-token');
            
            // Menggunakan Clipboard API untuk menyalin token
            navigator.clipboard.writeText(token).then(() => {
                // Tampilkan toast sukses jika ada
                if (toast) {
                    const toastBody = toastEl.querySelector('.toast-body');
                    if (toastBody) toastBody.textContent = 'Token berhasil disalin ke clipboard!';
                    toast.show();
                }
                
                // Ubah ikon untuk menunjukkan sukses
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check text-success"></i>';
                
                // Kembalikan ke ikon semula setelah 2 detik
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                }, 2000);
                
            }).catch(err => {
                console.error('Gagal menyalin token: ', err);
                
                // Tampilkan toast error jika ada
                if (toast) {
                    const toastBody = toastEl.querySelector('.toast-body');
                    if (toastBody) toastBody.textContent = 'Gagal menyalin token!';
                    toast.show();
                }
                
                // Ubah ikon untuk menunjukkan error
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-times text-danger"></i>';
                
                // Kembalikan ke ikon semula setelah 2 detik
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                }, 2000);
            });
        });
    });
}

// Panggil fungsi saat dokumen siap
document.addEventListener('DOMContentLoaded', setupTokenCopy);
    </script>
@endsection
