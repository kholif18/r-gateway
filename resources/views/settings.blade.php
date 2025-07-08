@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">System Configuration</div>
        </div>
        <div class="card-body">
            <form id="settings-form">
                <div class="settings-container">
                    <!-- Advanced Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <div>
                                <div class="section-title">Advance Setting</div>
                                <div class="section-description">Configure timeout, retry and other technical parameters</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="timeout">Delivery Timeout (seconds)</label>
                                <input type="number" id="timeout" name="timeout" class="form-control" value="{{ old('timeout', $settings['timeout'] ?? 30) }}" min="5" max="120">
                                <div class="form-hint">Maximum time to wait for a response when sending a message</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="max-retry">Maximum Retry Times</label>
                                <input type="number" id="max-retry" name="max-retry" class="form-control" value="{{ old('max-retry', $settings['max-retry'] ?? 3) }}" min="0" max="10">
                                <div class="form-hint">Number of retries if delivery fails</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="retry-interval">Retry Interval (seconds)</label>
                                <input type="number" id="retry-interval" name="retry-interval" class="form-control" value="{{ old('retry-interval', $settings['retry-interval'] ?? 10) }}" min="5" max="60">
                                <div class="form-hint">Waiting time between retries</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="max-queue">Maximum Messages in Queue</label>
                                <input type="number" id="max-queue" name="max-queue" class="form-control" value="{{ old('max-queue', $settings['max-queue'] ?? 100) }}" min="10" max="1000">
                                <div class="form-hint">Maximum number of messages that can be queued</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="rate_limit_limit">Rate Limit (max requests)</label>
                                <input type="number" id="rate_limit_limit" name="rate_limit_limit" class="form-control"
                                    value="{{ old('rate_limit_limit', $settings['rate_limit_limit'] ?? 5) }}"
                                    min="1" max="100">
                                <div class="form-hint">The maximum number of API requests allowed in a given period.</div>
                            </div>

                            <div class="form-group">
                                <label for="rate_limit_decay">Rate Limit Decay (seconds)</label>
                                <input type="number" id="rate_limit_decay" name="rate_limit_decay" class="form-control"
                                    value="{{ old('rate_limit_decay', $settings['rate_limit_decay'] ?? 60) }}"
                                    min="10" max="3600">
                                <div class="form-hint">Time period (in seconds) to reset the number of requests</div>
                            </div>
                        </div>
                    </div>
                    <!-- Country Code Selection -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="country_code">Default Country Code</label>
                            <select id="country_code" name="country_code" class="form-control">
                                <option value="62" {{ old('country_code', $settings['country_code'] ?? '') == '62' ? 'selected' : '' }}>🇮🇩 Indonesia (+62)</option>
                                <option value="60" {{ old('country_code', $settings['country_code'] ?? '') == '60' ? 'selected' : '' }}>🇲🇾 Malaysia (+60)</option>
                                <option value="65" {{ old('country_code', $settings['country_code'] ?? '') == '65' ? 'selected' : '' }}>🇸🇬 Singapore (+65)</option>
                                <option value="1"  {{ old('country_code', $settings['country_code'] ?? '') == '1'  ? 'selected' : '' }}>🇺🇸 United States (+1)</option>
                                <option value="91" {{ old('country_code', $settings['country_code'] ?? '') == '91' ? 'selected' : '' }}>🇮🇳 India (+91)</option>
                            </select>
                            <div class="form-hint">Select the default country code for phone number normalization</div>
                        </div>
                    </div>

                    <!-- Save Settings -->
                    <div class="settings-section">
                        <button class="btn" id="save-settings">
                            <i class="fas fa-save"></i> Save change
                        </button>
                        <button class="btn btn-outline" id="reset-settings">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Update Checker</h5>
            <button id="checkUpdateBtn" class="btn btn-info d-flex align-items-center">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="checkSpinner" role="status" aria-hidden="true"></span>
                Check Update
            </button>
        </div>
        <div class="card-body d-none" id="cardBodyContainer">
            <div id="updateResult"></div>
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
        document.getElementById('save-settings').addEventListener('click', function(e) {
            e.preventDefault(); // Tambahkan ini untuk cegah reload

            const formData = new FormData(document.getElementById('settings-form'));

            fetch('/settings/save', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || 'Gagal menyimpan') });
                }
                return res.json();
            })
            .then(data => {
                showToast('Success', 'Settings saved successfully!', 'success');
            })
            .catch(error => {
                showToast('Error', 'Failed to save settings: ' + error.message, 'error');
            });
        });

        function showToast(title, message, type = 'success') {
            const toastEl = document.getElementById('feedbackToast');
            const toast = new bootstrap.Toast(toastEl);

            const titleEl = document.getElementById('toastTitle');
            const bodyEl = document.getElementById('toastBody');
            const header = document.getElementById('toastHeader');

            // Reset
            header.className = 'toast-header';
            toastEl.classList.remove('bg-success', 'bg-danger');

            titleEl.innerText = title;
            bodyEl.innerText = message;

            if (type === 'success') {
                header.classList.add('bg-success', 'text-white');
                toastEl.classList.add('bg-success', 'text-white');
            } else {
                header.classList.add('bg-danger', 'text-white');
                toastEl.classList.add('bg-danger', 'text-white');
            }

            toast.show();
        }
        //Reset Settings
        document.getElementById('reset-settings').addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah form submit

            Swal.fire({
                title: 'Reset semua pengaturan?',
                text: 'Pengaturan akan dikembalikan ke default.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('settings.reset') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error("Reset gagal: " + response.statusText);
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Pengaturan berhasil direset.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => location.reload(), 1600);
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan: ' + error.message,
                            icon: 'error'
                        });
                        console.error('Reset error:', error);
                    });
                }
            });
        });

        // Handle update
        document.addEventListener('DOMContentLoaded', function () {
        const checkBtn = document.getElementById('checkUpdateBtn');
        const checkSpinner = document.getElementById('checkSpinner');
        const updateResult = document.getElementById('updateResult');
        const cardBody = document.getElementById('cardBodyContainer');

        // Handle Check Update
        checkBtn.addEventListener('click', function () {
            resetUI();
            toggleLoading(true);

            fetch('{{ route('update.check') }}')
                .then(response => handleFetchResponse(response))
                .then(data => {
                    cardBody.classList.remove('d-none');
                    renderUpdateResult(data);
                })
                .catch(error => showError('Gagal memeriksa pembaruan.', error))
                .finally(() => toggleLoading(false));
        });

        // Handle Install Update via delegation
        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'installUpdateBtn') {
                Swal.fire({
                    title: 'Konfirmasi Instalasi',
                    text: 'Apakah Anda yakin ingin menginstal pembaruan ini sekarang?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, instal sekarang',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const installBtn = e.target;
                        const installSpinner = document.getElementById('installSpinner');
                        const installStatus = document.getElementById('installStatus');

                        installBtn.disabled = true;
                        installSpinner.classList.remove('d-none');
                        installStatus.classList.remove('d-none', 'text-success', 'text-danger');
                        installStatus.textContent = 'Sedang menginstal pembaruan, mohon tunggu...';

                        fetch('{{ route('update.install') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => handleFetchResponse(response))
                        .then(data => {
                            installSpinner.classList.add('d-none');
                            installStatus.textContent = data.message;

                            if (data.success) {
                                installStatus.classList.add('text-success');
                                setTimeout(() => location.reload(), 3000);
                            } else {
                                installStatus.classList.add('text-danger');
                                installBtn.disabled = false;
                            }
                        })
                        .catch(error => {
                            installSpinner.classList.add('d-none');
                            installStatus.classList.add('text-danger');
                            installStatus.textContent = 'Terjadi kesalahan saat instalasi.';
                            installBtn.disabled = false;
                            console.error('Install error:', error);
                        });
                    }
                });
            }
        });

        // Helpers

        function resetUI() {
            cardBody.classList.add('d-none');
            updateResult.innerHTML = '';
        }

        function toggleLoading(loading) {
            checkSpinner.classList.toggle('d-none', !loading);
            checkBtn.disabled = loading;
        }

        function handleFetchResponse(response) {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response body:', text);
                    throw new Error(`HTTP error ${response.status}`);
                });
            }
            return response.json();
        }

        function renderUpdateResult(data) {
            if (data.update_available) {
                updateResult.innerHTML = `
                    <div class="alert alert-warning mb-3">${data.message}</div>
                    <button id="installUpdateBtn" class="btn btn-success d-flex align-items-center">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="installSpinner" role="status"></span>
                        Install Update
                    </button>
                    <div id="installStatus" class="mt-3 text-muted d-none">Mempersiapkan instalasi...</div>
                `;
            } else {
                updateResult.innerHTML = `
                    <div class="alert alert-success">${data.message}</div>
                `;
            }
        }

        function showError(userMessage, errorObj) {
            cardBody.classList.remove('d-none');
            updateResult.innerHTML = `<div class="alert alert-danger">${userMessage}</div>`;
            console.error(userMessage, errorObj);
        }
    });
    </script>
@endsection