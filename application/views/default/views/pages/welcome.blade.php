@extends('layouts.default')

@section('content')

<div class="content">
    <div class="content-grid">
        <div class="left-column">
            <div class="section">
                <h2 class="section-title">{{ lang('welcome_getting_started') }}</h2>
                <div class="card">
                    <p>{{ sprintf(lang('welcome_message'), $framework_name) }}</p>
                    <div class="file-path">{{ $file_path }}</div>
                    <p>{{ lang('welcome_edit_hint') }}</p>
                    <a href="/user_guide" class="docs-link">
                        {{ lang('welcome_view_docs') }} →
                    </a>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">{{ lang('welcome_requirements') }}</h2>
                <div class="card">
                    <ul class="features-list">
                        @foreach ($requirements as $requirement)
                            <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="right-column">
            <div class="section">
                <h2 class="section-title">{{ lang('welcome_features') }}</h2>
                <div class="features-grid">
                    @foreach ($features as $feature)
                        <div class="feature">
                            <div class="feature-icon">{{ $feature['icon'] }}</div>
                            <h3 style="margin-bottom: 8px; font-size: 16px;">{{ $feature['title'] }}</h3>
                            <p style="color: var(--text-secondary); font-size: 14px;">{{ $feature['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">{{ lang('welcome_quick_actions') }}</h2>
                <div class="card" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="/user_guide" class="btn">{{ lang('welcome_documentation') }}</a>
                    <a href="https://codeigniter.com" target="_blank" class="btn btn-outline">CodeIgniter.com</a>
                    <a href="https://github.com/bcit-ci/CodeIgniter" target="_blank" class="btn btn-outline">GitHub</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">{{ lang('welcome_system_info') }}</h2>
        <div class="card">
            <p>{{ lang('welcome_running_mode') }}
                <span class="environment-badge environment-{{ $environment }}">
                    {{ ucfirst($environment) }}
                </span>
            </p>
            
            <div class="system-info">
                <div class="info-item">
                    <span class="info-label">{{ lang('welcome_base_url') }}:</span>
                    <span class="info-value">{{ $base_url }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ lang('welcome_timezone') }}:</span>
                    <span class="info-value">{{ $timezone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ lang('welcome_server_software') }}:</span>
                    <span class="info-value">{{ $server_software }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ lang('welcome_document_root') }}:</span>
                    <span class="info-value">{{ $document_root }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="status-bar">
    <div class="status-item">
        <div class="status-value">{{ $elapsed_time }}s</div>
        <div class="status-label">{{ lang('welcome_render_time') }}</div>
    </div>
    <div class="status-item">
        <div class="status-value">
            {{ $framework_name }}
        </div>
        <div class="status-label">{{ lang('welcome_framework') }}</div>
    </div>
    <div class="status-item">
        <div class="status-value">PHP {{ $php_version }}</div>
        <div class="status-label">{{ lang('welcome_php_version') }}</div>
    </div>
    <div class="status-item">
        <div class="status-value">{{ date('Y-m-d H:i:s') }}</div>
        <div class="status-label">{{ lang('welcome_server_time') }}</div>
    </div>
</div>
    

@endsection