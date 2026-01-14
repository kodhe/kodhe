<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            background-color: var(--light-bg);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        
        .language-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
        }
        
        .lang-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .lang-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .lang-btn.active {
            background: white;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .framework-name {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .framework-version {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .tagline {
            font-size: 16px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .badges {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .content {
            padding: 40px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .language-switcher {
                position: relative;
                top: 0;
                right: 0;
                justify-content: center;
                margin-bottom: 20px;
            }
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 18px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .card {
            background: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 16px;
        }
        
        .card:hover {
            border-color: var(--primary-color);
            transition: border-color 0.2s;
        }
        
        .file-path {
            font-family: 'SF Mono', Monaco, Consolas, monospace;
            background: var(--text-primary);
            color: #e2e8f0;
            padding: 12px;
            border-radius: 4px;
            margin: 12px 0;
            font-size: 13px;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .features-list {
            list-style: none;
        }
        
        .features-list li {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .features-list li:last-child {
            border-bottom: none;
        }
        
        .features-list li::before {
            content: '✓';
            color: var(--success-color);
            font-weight: 600;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .feature {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        .feature-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .status-bar {
            background: #f1f5f9;
            border-top: 1px solid var(--border-color);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .status-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 100px;
        }
        
        .status-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 4px;
        }
        
        .status-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background: var(--primary-dark);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .docs-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .docs-link:hover {
            text-decoration: underline;
        }
        
        .environment-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .environment-development {
            background: #fef3c7;
            color: #92400e;
        }
        
        .environment-production {
            background: #d1fae5;
            color: #065f46;
        }
        
        .footer {
            text-align: center;
            padding: 30px 40px;
            color: var(--text-secondary);
            font-size: 14px;
            border-top: 1px solid var(--border-color);
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
        }
        
        .footer-links a:hover {
            color: var(--primary-color);
        }
        
        .system-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed var(--border-color);
        }
        
        .info-label {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .info-value {
            font-weight: 500;
            font-size: 14px;
            font-family: 'SF Mono', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="language-switcher">
                <a href="{{ site_url('welcome/switch_language/id') }}" 
                   class="lang-btn {{ ($lang == 'id') ? 'active' : '' }}">
                    🇮🇩 ID
                </a>
                <a href="{{ site_url('welcome/switch_language/en') }}" 
                   class="lang-btn {{ ($lang == 'en') ? 'active' : '' }}">
                    🇬🇧 EN
                </a>
            </div>
            
            <h1 class="framework-name">{{ $framework_name }}</h1>
            <div class="framework-version">{{ $version_text }}</div>
            <p class="tagline">{{ $tagline }}</p>
            
            <div class="badges">
                @foreach ($badges as $badge)
                    <span class="badge">{{ $badge }}</span>
                @endforeach
            </div>
        </div>
        
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
        
        <div class="footer">
            <p>© {{ $current_year }} {{ $framework_name }}. {{ lang('welcome_rights_reserved') }}</p>
            <div class="footer-links">
                <a href="/user_guide">{{ lang('welcome_documentation') }}</a>
                <a href="https://github.com/bcit-ci/CodeIgniter">GitHub</a>
                <a href="/license">{{ lang('welcome_license') }}</a>
                <a href="https://codeigniter.com/userguide3/changelog.html">{{ lang('welcome_changelog') }}</a>
            </div>
            <p style="margin-top: 15px; font-size: 12px; opacity: 0.7;">
                CodeIgniter {{ CI_VERSION }} - {{ lang('welcome_legacy_note') }}
            </p>
        </div>
    </div>

    <script type="text/javascript">
        // Update server time every minute
        function updateServerTime() {
            const now = new Date();
            const timeElements = document.querySelectorAll('.status-value');
            if (timeElements.length >= 4) {
                const formatted = now.toISOString().slice(0, 19).replace('T', ' ');
                timeElements[3].textContent = formatted;
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Update server time every minute
            setInterval(updateServerTime, 60000);
            
            // Add animation to features
            const features = document.querySelectorAll('.feature');
            features.forEach(feature => {
                feature.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.transition = 'transform 0.2s ease';
                });
                feature.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>