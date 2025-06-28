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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
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

            document.getElementById('toastTitle').innerText = title;
            document.getElementById('toastBody').innerText = message;

            const header = document.getElementById('toastHeader');
            header.className = 'toast-header';
            if (type === 'success') header.classList.add('bg-success', 'text-white');
            else if (type === 'error') header.classList.add('bg-danger', 'text-white');

            toast.show();
        }
        //Reset Settings
        document.getElementById('reset-settings').addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah form submit
            
            if (confirm('Reset all setting?')) {
                fetch("{{ route('settings.reset') }}", { // Gunakan named route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error("Reset failed: " + response.statusText);
                    return response.json();
                })
                .then(data => {
                    showToast('Success', 'Settings successfully reset!', 'success');
                    setTimeout(() => location.reload(), 1500); // Reload setelah toast tampil
                })
                .catch(error => {
                    showToast('Error', 'Reset failed: ' + error.message, 'error');
                    console.error('Reset error:', error);
                });
            }
        });
    </script>
@endsection