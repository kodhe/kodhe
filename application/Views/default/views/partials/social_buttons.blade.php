{{--
    Shared social-login button list (kodhe/socialite native OAuth).

    Expects $socialProviders: array of ['name','label','color','connected']
    built by AuthController::socialButtons(). Renders nothing when the list
    is empty, so classic password login keeps working unchanged.
--}}
@if(!empty($socialProviders))
    <div class="social-divider"><span>or continue with</span></div>

    <div class="social-buttons">
        @foreach($socialProviders as $sp)
            <a class="social-btn" style="--brand: {{ $sp['color'] }}"
               href="{{ $base_url }}auth/socialite/{{ $sp['name'] }}">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                    @switch($sp['name'])
                        @case('google')
                            <path d="M12 11v3.2h5.1c-.2 1.3-1.6 3.9-5.1 3.9-3.1 0-5.6-2.6-5.6-5.7s2.5-5.7 5.6-5.7c1.7 0 2.9.7 3.6 1.4l2.4-2.3C16.5 4.4 14.5 3.5 12 3.5 7.3 3.5 3.5 7.3 3.5 12s3.8 8.5 8.5 8.5c4.9 0 8.2-3.5 8.2-8.4 0-.6-.1-1-.1-1.6H12z"/>
                            @break
                        @case('github')
                            <path d="M12 2A10 10 0 0 0 8.8 21.5c.5.1.7-.2.7-.5v-1.7c-2.8.6-3.4-1.2-3.4-1.2-.5-1.2-1.1-1.5-1.1-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.6 2.4 1.1 3 .9.1-.7.4-1.1.6-1.4-2.2-.3-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.8 1a9.6 9.6 0 0 1 5.1 0c2-1.3 2.8-1 2.8-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.4 4.7-4.6 5 .4.3.7.9.7 1.9v2.8c0 .3.2.6.7.5A10 10 0 0 0 12 2z"/>
                            @break
                        @case('facebook')
                            <path d="M13.5 21v-7.5h2.5l.5-3h-3V8.6c0-.9.2-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H8v3h2.5V21h3z"/>
                            @break
                        @case('gitlab')
                            <path d="m2.2 9.7-3 9.2a.8.8 0 0 0 1.1 1l3.9-3-2-7.2zm4.2 0h11.2l-5.6 17.2a.5.5 0 0 1-1 0L4.3 9.7zM1 9.7 4.3 9.7l2 7.2-3.9 3a.8.8 0 0 1-1.1-1L1 9.7zm20.8 0 3 9.2a.8.8 0 0 1-1.1 1l-3.9-3 2-7.2zm-1.5 0h3.3l-3 9.2a.8.8 0 0 1-1.1 1l-3.9-3 2-7.2z" transform="scale(.9) translate(1.3 0)"/>
                            @break
                        @case('linkedin')
                            <path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3V9zm7 0h3.8v1.7h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.5c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21h-4V9z"/>
                            @break
                        @case('twitter')
                            <path d="M18.9 2H22l-6.8 7.8L23.3 22h-6.3l-4.9-6.4L6.5 22H3.4l7.3-8.3L1.2 2h6.4l4.4 5.9L18.9 2zm-1.1 18h1.7L7.7 3.7H5.8L17.8 20z"/>
                            @break
                        @default
                            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/>
                    @endswitch
                </svg>
                <span>{{ $sp['label'] }}</span>
                @if(!empty($sp['connected']))
                    <span class="social-connected" title="Already connected">&#10003;</span>
                @endif
            </a>
        @endforeach
    </div>

    <style>
    .social-divider { display: flex; align-items: center; gap: 12px; margin: 22px 0 14px;
                      color: var(--text-secondary); font-size: 13px; }
    .social-divider::before, .social-divider::after { content: ""; flex: 1; height: 1px; background: var(--border-color); }
    .social-buttons { display: grid; gap: 10px; }
    .social-btn { display: flex; align-items: center; justify-content: center; gap: 10px;
                  padding: 11px 14px; border: 1px solid var(--border-color); border-radius: 8px;
                  background: #fff; color: var(--brand, #333); font-weight: 600; font-size: 15px;
                  text-decoration: none; transition: box-shadow .15s, border-color .15s; }
    .social-btn:hover { border-color: var(--brand); box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .social-btn svg { color: var(--brand); flex: 0 0 auto; }
    .social-btn span { color: #374151; }
    .social-connected { color: #15803d; font-weight: 700; }
    </style>
@endif
