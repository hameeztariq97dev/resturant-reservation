<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexium AI — Reservation Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #6366F1;
            --color-primary-dark: #4F46E5;
            --color-primary-light: #818CF8;
            --color-bg-dark: #0F172A;
            --color-surface-dark: #1E293B;
            --color-surface-light: #334155;
            --color-border: #334155;
            --color-text-primary: #F1F5F9;
            --color-text-secondary: #94A3B8;
            --color-user-bubble: #6366F1;
            --color-bot-bubble: #1E293B;
            --color-success: #10B981;
            --color-error: #F87171;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--color-bg-dark);
            color: var(--color-text-primary);
        }

        .chat-page {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
            padding: 1.5rem;
            background: var(--color-bg-dark);
        }

        .chat-shell {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: min(720px, calc(100vh - 3rem));
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--color-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background: var(--color-surface-dark);
            border-right: 1px solid var(--color-border);
            border-radius: 16px 0 0 16px;
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .sidebar-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-icon svg {
            width: 24px;
            height: 24px;
            color: #fff;
        }

        .sidebar-brand-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--color-text-primary);
            margin: 0;
        }

        .sidebar-brand-sub {
            font-size: 0.8rem;
            color: var(--color-text-secondary);
            margin: 0.15rem 0 0;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--color-border);
            margin: 0.5rem 0 1rem;
        }

        .sidebar-cards {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .info-card {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: var(--color-bg-dark);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 12px;
        }

        .info-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-card-icon--indigo { background: rgba(99, 102, 241, 0.2); color: var(--color-primary-light); }
        .info-card-icon--red { background: rgba(248, 113, 113, 0.15); color: #F87171; }
        .info-card-icon--green { background: rgba(16, 185, 129, 0.2); color: var(--color-success); }

        .info-card-title {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--color-text-primary);
            margin: 0 0 0.2rem;
        }

        .info-card-desc {
            font-size: 0.75rem;
            color: var(--color-text-secondary);
            margin: 0;
            line-height: 1.35;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--color-border);
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--color-text-primary);
            margin-bottom: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-success);
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        }

        .powered-by {
            font-size: 0.7rem;
            color: var(--color-text-secondary);
            margin: 0;
        }

        /* Main panel */
        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--color-bg-dark);
            border-radius: 0 16px 16px 0;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--color-surface-dark);
            border-bottom: 1px solid var(--color-border);
        }

        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chat-header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
        }

        .chat-header-titles h1 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .chat-header-titles h1 .mobile-brand {
            display: none;
        }

        .chat-header-sub {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            color: var(--color-text-secondary);
            margin-top: 0.15rem;
        }

        .chat-header-sub .dot-online {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--color-success);
        }

        .badge-ai {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.25);
            color: var(--color-primary-light);
            border: 1px solid rgba(99, 102, 241, 0.35);
        }

        .messages-wrap {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .messages-wrap::-webkit-scrollbar {
            width: 6px;
        }

        .messages-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .messages-wrap::-webkit-scrollbar-thumb {
            background: var(--color-primary);
            border-radius: 3px;
        }

        .msg-row {
            display: flex;
            flex-direction: column;
            max-width: 75%;
            animation: msgIn 0.3s ease forwards;
        }

        @keyframes msgIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg-row--user {
            align-self: flex-end;
            align-items: flex-end;
        }

        .msg-row--bot {
            align-self: flex-start;
        }

        .msg-row--error .msg-bubble--bot {
            border-color: rgba(248, 113, 113, 0.5);
            background: rgba(127, 29, 29, 0.25);
        }

        .msg-bubble {
            padding: 12px 16px;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.45;
            word-break: break-word;
        }

        .msg-bubble--bot {
            background: var(--color-bot-bubble);
            border: 1px solid var(--color-border);
            border-radius: 4px 16px 16px 16px;
            color: var(--color-text-primary);
        }

        .msg-bubble--user {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            border-radius: 16px 4px 16px 16px;
            color: #fff;
        }

        .msg-time {
            font-size: 0.75rem;
            color: var(--color-text-secondary);
            margin-top: 0.35rem;
            padding: 0 4px;
        }

        .typing-bubble {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 14px 18px;
            background: var(--color-bot-bubble);
            border: 1px solid var(--color-border);
            border-radius: 4px 16px 16px 16px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-primary);
            animation: typingPulse 1.2s ease infinite;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.15s; }
        .typing-dot:nth-child(3) { animation-delay: 0.3s; }

        @keyframes typingPulse {
            0%, 80%, 100% { opacity: 0.35; transform: scale(0.9); }
            40% { opacity: 1; transform: scale(1); }
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0 1.5rem 0.75rem;
        }

        .chips--hidden {
            display: none;
        }

        .chip {
            font-family: inherit;
            font-size: 0.85rem;
            padding: 6px 16px;
            border-radius: 999px;
            border: 1px solid var(--color-primary);
            color: var(--color-primary-light);
            background: transparent;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .chip:hover {
            background: rgba(99, 102, 241, 0.2);
        }

        .chip:active {
            transform: scale(0.98);
        }

        .input-area {
            padding: 1rem 1.5rem;
            background: var(--color-surface-dark);
            border-top: 1px solid var(--color-border);
        }

        .input-row {
            display: flex;
            align-items: flex-end;
            gap: 0.75rem;
        }

        .input-wrap {
            flex: 1;
            min-width: 0;
        }

        .msg-input {
            width: 100%;
            min-height: 48px;
            max-height: 120px;
            resize: none;
            font-family: inherit;
            font-size: 0.9rem;
            line-height: 1.45;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--color-border);
            background: var(--color-bg-dark);
            color: var(--color-text-primary);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .msg-input::placeholder {
            color: var(--color-text-secondary);
        }

        .msg-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .btn-send {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: #fff;
            transition: filter 0.15s ease, transform 0.15s ease;
        }

        .btn-send:hover:not(:disabled) {
            filter: brightness(1.1);
            transform: scale(1.05);
        }

        .btn-send:active:not(:disabled) {
            transform: scale(0.95);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-send svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 767px) {
            .chat-page {
                padding: 0.75rem;
            }

            .sidebar {
                display: none;
            }

            .main-panel {
                border-radius: 16px;
            }

            .chat-shell {
                border-radius: 16px;
            }

            .chat-header-titles h1 .mobile-brand {
                display: inline;
            }

            .chat-header-titles h1 .desktop-title {
                display: none;
            }

            .msg-row {
                max-width: 90%;
            }
        }

        @media (min-width: 768px) {
            .chat-header-titles h1 .mobile-brand {
                display: none;
            }

            .chat-header-titles h1 .desktop-title {
                display: inline;
            }
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>
<body>
    <div class="chat-page">
        <div class="chat-shell">
            <aside class="sidebar" aria-label="Assistant info">
                <div class="sidebar-brand">
                    <div class="sidebar-brand-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="sidebar-brand-title">Nexium AI</p>
                        <p class="sidebar-brand-sub">Reservation Assistant</p>
                    </div>
                </div>
                <div class="sidebar-divider"></div>
                <div class="sidebar-cards">
                    <div class="info-card">
                        <div class="info-card-icon info-card-icon--indigo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="info-card-title">Create Booking</p>
                            <p class="info-card-desc">Book a table instantly</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-icon info-card-icon--red">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="info-card-title">Cancel Booking</p>
                            <p class="info-card-desc">Cancel with your code</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-icon info-card-icon--green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="info-card-title">Instant Confirm</p>
                            <p class="info-card-desc">Get your code instantly</p>
                        </div>
                    </div>
                </div>
                <div class="sidebar-footer">
                    <div class="status-row">
                        <span class="status-dot" aria-hidden="true"></span>
                        <span>AI Online</span>
                    </div>
                    <p class="powered-by">Powered by Gemini AI</p>
                </div>
            </aside>

            <div class="main-panel">
                <header class="chat-header">
                    <div class="chat-header-left">
                        <div class="chat-header-avatar" aria-hidden="true">N</div>
                        <div class="chat-header-titles">
                            <h1>
                                <span class="desktop-title">Nexium Assistant</span>
                                <span class="mobile-brand">Nexium AI</span>
                            </h1>
                            <div class="chat-header-sub">
                                <span class="dot-online" aria-hidden="true"></span>
                                <span>Online</span>
                            </div>
                        </div>
                    </div>
                    <span class="badge-ai">AI Powered</span>
                </header>

                <div class="messages-wrap" id="messages" role="log" aria-live="polite" aria-relevant="additions"></div>

                <div class="chips" id="chips">
                    <button type="button" class="chip" data-chip="📅 Make a Reservation">📅 Make a Reservation</button>
                    <button type="button" class="chip" data-chip="❌ Cancel Reservation">❌ Cancel Reservation</button>
                    <button type="button" class="chip" data-chip="ℹ️ How does it work?">ℹ️ How does it work?</button>
                </div>

                <div class="input-area">
                    <div class="input-row">
                        <div class="input-wrap">
                            <label for="msg-input" class="sr-only">Message</label>
                            <textarea
                                id="msg-input"
                                class="msg-input"
                                rows="1"
                                placeholder="Ask me about reservations..."
                                autocomplete="off"
                            ></textarea>
                        </div>
                        <button type="button" class="btn-send" id="btn-send" aria-label="Send message" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const WEBHOOK_URL = @json(config('services.n8n.webhook_url', 'http://127.0.0.1:5678/webhook/reservation-agent'));

            const messagesEl = document.getElementById('messages');
            const chipsEl = document.getElementById('chips');
            const inputEl = document.getElementById('msg-input');
            const btnSend = document.getElementById('btn-send');

            let messages = [];
            let isLoading = false;
            let chipsVisible = true;

            function formatTime(d) {
                return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
            }

            function scrollToBottom() {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function appendMessage(role, text, isError) {
                const row = document.createElement('div');
                row.className = 'msg-row msg-row--' + (role === 'user' ? 'user' : 'bot') + (isError ? ' msg-row--error' : '');

                const bubble = document.createElement('div');
                bubble.className = 'msg-bubble msg-bubble--' + (role === 'user' ? 'user' : 'bot');
                bubble.textContent = text;

                row.appendChild(bubble);

                const time = document.createElement('div');
                time.className = 'msg-time';
                time.textContent = formatTime(new Date());
                row.appendChild(time);

                messagesEl.appendChild(row);
                scrollToBottom();
            }

            function showWelcome() {
                const welcome = "👋 Welcome to Nexium Reservations! I can help you book or cancel a table. What would you like to do today?";
                appendMessage('bot', welcome, false);
            }

            function showTyping() {
                const wrap = document.createElement('div');
                wrap.className = 'msg-row msg-row--bot';
                wrap.id = 'typing-indicator';
                const inner = document.createElement('div');
                inner.className = 'typing-bubble';
                inner.setAttribute('aria-label', 'Assistant is typing');
                for (let i = 0; i < 3; i++) {
                    const dot = document.createElement('span');
                    dot.className = 'typing-dot';
                    inner.appendChild(dot);
                }
                wrap.appendChild(inner);
                messagesEl.appendChild(wrap);
                scrollToBottom();
            }

            function hideTyping() {
                const el = document.getElementById('typing-indicator');
                if (el) el.remove();
            }

            function extractReply(data) {
                if (data == null) return '';
                if (typeof data === 'string') return data;
                if (typeof data === 'object') {
                    if (typeof data.output === 'string') return data.output;
                    if (typeof data.response === 'string') return data.response;
                    if (Array.isArray(data) && data[0]) {
                        const first = data[0];
                        if (first.json && typeof first.json.output === 'string') return first.json.output;
                        if (typeof first.output === 'string') return first.output;
                    }
                }
                try {
                    return JSON.stringify(data);
                } catch (e) {
                    return '';
                }
            }

            async function sendUserMessage(userText) {
                if (!userText.trim() || isLoading) return;

                if (chipsVisible) {
                    chipsEl.classList.add('chips--hidden');
                    chipsVisible = false;
                }

                appendMessage('user', userText.trim(), false);
                inputEl.value = '';
                resizeTextarea();
                updateSendState();

                isLoading = true;
                showTyping();
                btnSend.disabled = true;

                try {
                    const res = await fetch(WEBHOOK_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ user_message: userText.trim() }),
                    });

                    const raw = await res.text();
                    let data;
                    try {
                        data = raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        data = { _raw: raw };
                    }

                    hideTyping();

                    if (!res.ok) {
                        appendMessage('bot', 'Request failed (' + res.status + '). ' + (raw.slice(0, 200) || ''), true);
                        return;
                    }

                    const reply = extractReply(data) || '';
                    if (!reply && data._raw) {
                        appendMessage('bot', 'Unexpected response from server.', true);
                        return;
                    }
                    appendMessage('bot', reply || '…', false);
                } catch (err) {
                    hideTyping();
                    const msg = err && err.message ? err.message : 'Network error';
                    appendMessage('bot', 'Could not reach the assistant. ' + msg + ' If the page is on a different origin than n8n, enable CORS on the webhook or use a server proxy.', true);
                } finally {
                    isLoading = false;
                    btnSend.disabled = false;
                    updateSendState();
                    scrollToBottom();
                }
            }

            function updateSendState() {
                btnSend.disabled = isLoading || !inputEl.value.trim();
            }

            function resizeTextarea() {
                inputEl.style.height = 'auto';
                const line = 24;
                const maxH = 4 * line + 24;
                inputEl.style.height = Math.min(inputEl.scrollHeight, maxH) + 'px';
            }

            inputEl.addEventListener('input', function () {
                resizeTextarea();
                updateSendState();
            });

            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendUserMessage(inputEl.value);
                }
            });

            btnSend.addEventListener('click', function () {
                sendUserMessage(inputEl.value);
            });

            chipsEl.querySelectorAll('.chip').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const text = btn.getAttribute('data-chip') || btn.textContent;
                    inputEl.value = text;
                    resizeTextarea();
                    updateSendState();
                    sendUserMessage(text);
                });
            });

            showWelcome();
            updateSendState();
        })();
    </script>
</body>
</html>
