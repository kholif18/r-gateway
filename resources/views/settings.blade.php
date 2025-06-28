@extends('components.app')

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

    <script>
        // Save settings
        document.getElementById('save-settings').addEventListener('click', function() {
            const formData = new FormData(document.querySelector('form')); // pastikan semua input ada dalam <form>
            fetch('/settings/save', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json()).then(data => {
                alert('Settings saved successfully!');
            });
        });

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
                    alert('Settings successfully reset!');
                    location.reload(); // Reload halaman
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    console.error('Reset error:', error);
                });
            }
        });
    </script>
@endsection