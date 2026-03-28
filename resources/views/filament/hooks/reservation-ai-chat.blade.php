@once
    {{-- Filament ships pre-built CSS; arbitrary Tailwind classes in this hook are NOT compiled, so we use scoped plain CSS. --}}
    <style>
        /* Light theme (Filament default). Dark: html.dark */
        .fi-ra-root {
            --fi-ra-bg: #ffffff;
            --fi-ra-surface: #f8fafc;
            --fi-ra-scroll-bg: #f1f5f9;
            --fi-ra-border: #e2e8f0;
            --fi-ra-text: #0f172a;
            --fi-ra-muted: #64748b;
            --fi-ra-primary: #6366f1;
            --fi-ra-primary-dark: #4f46e5;
            --fi-ra-primary-light: #4f46e5;
            --fi-ra-bot: #f1f5f9;
            --fi-ra-blue-ring: #a5b4fc;
            --fi-ra-panel-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(99, 102, 241, 0.1);
            --fi-ra-input-focus: rgba(99, 102, 241, 0.2);
            --fi-ra-card-accent-bg: rgba(99, 102, 241, 0.08);
            --fi-ra-card-accent-border: rgba(99, 102, 241, 0.25);
        }

        html.dark .fi-ra-root {
            --fi-ra-bg: #0f172a;
            --fi-ra-surface: #1e293b;
            --fi-ra-scroll-bg: #020617;
            --fi-ra-border: #334155;
            --fi-ra-text: #f1f5f9;
            --fi-ra-muted: #94a3b8;
            --fi-ra-primary: #6366f1;
            --fi-ra-primary-dark: #4f46e5;
            --fi-ra-primary-light: #818cf8;
            --fi-ra-bot: #1e293b;
            --fi-ra-blue-ring: #3b82f6;
            --fi-ra-panel-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(99, 102, 241, 0.12);
            --fi-ra-input-focus: rgba(99, 102, 241, 0.25);
            --fi-ra-card-accent-bg: rgba(99, 102, 241, 0.12);
            --fi-ra-card-accent-border: rgba(99, 102, 241, 0.35);
        }

        .fi-ra-dock {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 99999;
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1rem 1.25rem;
            padding-left: max(1rem, env(safe-area-inset-left));
            padding-right: max(1rem, env(safe-area-inset-right));
            padding-bottom: max(1.25rem, env(safe-area-inset-bottom));
            pointer-events: none;
        }

        .fi-ra-col {
            display: flex;
            width: min(26rem, calc(100vw - 2rem));
            flex-direction: column;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 0.75rem;
            pointer-events: none;
        }

        .fi-ra-panel {
            pointer-events: auto;
            width: 100%;
            overflow: hidden;
            border-radius: 1.5rem;
            border: 1px solid var(--fi-ra-border);
            isolation: isolate;
            background-color: var(--fi-ra-bg);
            color: var(--fi-ra-text);
            box-shadow: var(--fi-ra-panel-shadow);
        }

        .fi-ra-header {
            position: relative;
            z-index: 2;
            isolation: isolate;
            border-bottom: 1px solid rgba(0, 0, 0, 0.12);
            /* Solid base so title never blends with page content behind the panel */
            background-color: #4338ca;
            background-image: linear-gradient(135deg, var(--fi-ra-primary) 0%, var(--fi-ra-primary-dark) 55%, #4338ca 100%);
            background-repeat: no-repeat;
            padding: 1rem 1rem;
            color: #fff;
        }

        html.dark .fi-ra-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .fi-ra-header-inner {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .fi-ra-icon-wrap {
            display: flex;
            height: 3rem;
            width: 3rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.18);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .fi-ra-icon-wrap svg {
            width: 1.75rem;
            height: 1.75rem;
            color: #FDE68A;
        }

        .fi-ra-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }

        .fi-ra-sub {
            margin: 0.2rem 0 0;
            font-size: 0.75rem;
            opacity: 0.95;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        .fi-ra-close {
            flex-shrink: 0;
            border: none;
            border-radius: 0.75rem;
            padding: 0.5rem;
            background: transparent;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
        }

        .fi-ra-close:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .fi-ra-close svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .fi-ra-scroll {
            max-height: min(22rem, 50dvh);
            overflow-y: auto;
            padding: 1rem;
            background-color: var(--fi-ra-scroll-bg);
            font-size: 0.8125rem;
            line-height: 1.5;
        }

        .fi-ra-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .fi-ra-scroll::-webkit-scrollbar-thumb {
            background: var(--fi-ra-primary);
            border-radius: 3px;
        }

        .fi-ra-intro {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .fi-ra-intro > p {
            margin: 0;
            color: var(--fi-ra-muted);
        }

        .fi-ra-card {
            border-radius: 0.75rem;
            border: 1px solid var(--fi-ra-border);
            padding: 0.75rem;
        }

        .fi-ra-card--accent {
            background: var(--fi-ra-card-accent-bg);
            border-color: var(--fi-ra-card-accent-border);
        }

        .fi-ra-card--muted {
            background: var(--fi-ra-surface);
        }

        .fi-ra-card-label {
            margin: 0 0 0.25rem;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--fi-ra-primary-light);
        }

        .fi-ra-card-label--dim {
            color: var(--fi-ra-muted);
        }

        .fi-ra-card p:last-child {
            margin: 0;
            font-size: 0.75rem;
            color: var(--fi-ra-text);
        }

        .fi-ra-row {
            display: flex;
            margin-bottom: 0.75rem;
        }

        .fi-ra-row--user {
            justify-content: flex-end;
        }

        .fi-ra-row--bot {
            justify-content: flex-start;
        }

        .fi-ra-bubble {
            max-width: 90%;
            padding: 0.625rem 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            word-break: break-word;
        }

        .fi-ra-bubble--user {
            border-bottom-right-radius: 0.25rem;
            background: linear-gradient(135deg, var(--fi-ra-primary), var(--fi-ra-primary-dark));
            color: #fff;
        }

        .fi-ra-bubble--bot {
            border: 1px solid var(--fi-ra-border);
            border-bottom-left-radius: 0.25rem;
            border-top-left-radius: 0.25rem;
            background: var(--fi-ra-bot);
            color: var(--fi-ra-text);
        }

        .fi-ra-bubble p {
            margin: 0;
            white-space: pre-wrap;
        }

        .fi-ra-typing {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            border: 1px solid var(--fi-ra-border);
            background: var(--fi-ra-surface);
        }

        .fi-ra-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--fi-ra-primary);
            animation: fiRaDot 1.2s ease infinite;
        }

        .fi-ra-dot:nth-child(2) {
            animation-delay: 0.15s;
        }

        .fi-ra-dot:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes fiRaDot {
            0%, 80%, 100% {
                opacity: 0.35;
                transform: scale(0.9);
            }
            40% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .fi-ra-composer {
            border-top: 1px solid var(--fi-ra-border);
            background-color: var(--fi-ra-surface);
            padding: 0.75rem;
        }

        .fi-ra-composer-row {
            display: flex;
            gap: 0.5rem;
        }

        .fi-ra-input {
            min-width: 0;
            flex: 1;
            border-radius: 0.75rem;
            border: 1px solid var(--fi-ra-border);
            background: var(--fi-ra-bg);
            padding: 0.625rem 0.875rem;
            font-family: inherit;
            font-size: 0.875rem;
            color: var(--fi-ra-text);
        }

        .fi-ra-input::placeholder {
            color: var(--fi-ra-muted);
        }

        .fi-ra-input:focus {
            outline: none;
            border-color: var(--fi-ra-primary);
            box-shadow: 0 0 0 3px var(--fi-ra-input-focus);
        }

        .fi-ra-send {
            flex-shrink: 0;
            cursor: pointer;
            border: none;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--fi-ra-primary), var(--fi-ra-primary-dark));
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .fi-ra-send:hover:not(:disabled) {
            filter: brightness(1.08);
        }

        .fi-ra-send:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .fi-ra-err {
            margin: 0.5rem 0 0;
            font-size: 0.75rem;
            color: #F87171;
        }

        .fi-ra-fab {
            pointer-events: auto;
            position: relative;
            display: flex;
            height: 4.25rem;
            width: 4.25rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 5px solid var(--fi-ra-blue-ring);
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #7C3AED 0%, var(--fi-ra-primary) 45%, #2563EB 100%);
            color: #fff;
            box-shadow: 0 16px 40px -10px rgba(37, 99, 235, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .fi-ra-fab:hover {
            transform: scale(1.05);
        }

        .fi-ra-fab:focus-visible {
            outline: 2px solid var(--fi-ra-primary-light);
            outline-offset: 3px;
        }

        .fi-ra-fab svg {
            position: relative;
            width: 2.25rem;
            height: 2.25rem;
        }
    </style>
    <script>
        function filamentReservationAiChat() {
            const offlineMessage = @json(__('We could not reach the assistant. Try again in a moment.'));
            const chatUrl = @js(route('filament.admin.n8n-chat'));

            return {
                open: false,
                loading: false,
                messages: [],
                input: '',
                error: null,
                chatUrl: chatUrl,
                async send() {
                    if (!this.input.trim() || this.loading) {
                        return;
                    }
                    const text = this.input.trim();
                    this.input = '';
                    this.messages.push({ role: 'user', content: text });
                    this.error = null;
                    this.loading = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(this.chatUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': token ?? '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ user_message: text }),
                        });
                        const data = await res.json();
                        if (!data.success) {
                            this.error = data.message ?? 'Request failed';
                            let errText = data.message ?? 'Something went wrong.';
                            if (data.detail) {
                                errText += ' (' + String(data.detail).slice(0, 200) + ')';
                            }
                            this.messages.push({ role: 'assistant', content: errText });
                        } else {
                            this.messages.push({ role: 'assistant', content: data.reply || '…' });
                        }
                    } catch (e) {
                        this.error = e instanceof Error ? e.message : 'Error';
                        this.messages.push({ role: 'assistant', content: offlineMessage });
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => {
                            const el = this.$refs.scrollArea;
                            if (el) {
                                el.scrollTop = el.scrollHeight;
                            }
                        });
                    }
                },
            };
        }
    </script>
@endonce

<div wire:ignore x-data="filamentReservationAiChat()">
    <template x-teleport="body">
        <div class="fi-ra-root fi-ra-dock">
            <div class="fi-ra-col">
                <div
                    x-show="open"
                    class="fi-ra-panel"
                    style="display: none;"
                    x-on:keydown.escape.window="open = false"
                >
                    <div class="fi-ra-header">
                        <div class="fi-ra-header-inner">
                            <div style="display: flex; min-width: 0; align-items: center; gap: 0.75rem;">
                                <span class="fi-ra-icon-wrap" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2.5l2.2 5.5h4.8l-3.9 2.8 1.5 5.2L12 14.8 7.4 16l1.5-5.2L5 8h4.8L12 2.5z" fill="currentColor"/>
                                        <path d="M6 18h12v2H6v-2z" fill="currentColor" opacity="0.95"/>
                                    </svg>
                                </span>
                                <div style="min-width: 0;">
                                    <p class="fi-ra-title">{{ __('Reservation assistant') }}</p>
                                    <p class="fi-ra-sub">{{ __('Book a table or cancel a booking') }}</p>
                                </div>
                            </div>
                            <button type="button" class="fi-ra-close" x-on:click="open = false" aria-label="{{ __('Close') }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <div x-ref="scrollArea" class="fi-ra-scroll">
                        <template x-if="messages.length === 0">
                            <div class="fi-ra-intro">
                                <p>{{ __('Hi — I can help you reserve a table or cancel an existing booking.') }}</p>
                                <div class="fi-ra-card fi-ra-card--accent">
                                    <p class="fi-ra-card-label">{{ __('New reservation') }}</p>
                                    <p>{{ __('You can send everything in one message (name, email, phone, party size, and date), or answer step by step.') }}</p>
                                </div>
                                <div class="fi-ra-card fi-ra-card--muted">
                                    <p class="fi-ra-card-label fi-ra-card-label--dim">{{ __('Cancel') }}</p>
                                    <p>{{ __('Have your confirmation code ready.') }}</p>
                                </div>
                            </div>
                        </template>
                        <template x-for="(m, i) in messages" :key="i">
                            <div class="fi-ra-row" :class="m.role === 'user' ? 'fi-ra-row--user' : 'fi-ra-row--bot'">
                                <div class="fi-ra-bubble" :class="m.role === 'user' ? 'fi-ra-bubble--user' : 'fi-ra-bubble--bot'">
                                    <p x-text="m.content"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="loading" class="fi-ra-row fi-ra-row--bot" style="display: none;">
                            <div class="fi-ra-typing" aria-hidden="true">
                                <span class="fi-ra-dot"></span>
                                <span class="fi-ra-dot"></span>
                                <span class="fi-ra-dot"></span>
                                <span style="margin-left: 0.25rem; font-size: 0.75rem; color: var(--fi-ra-muted);">{{ __('Thinking…') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="fi-ra-composer">
                        <div class="fi-ra-composer-row">
                            <input
                                type="text"
                                name="reservation-assistant-message"
                                class="fi-ra-input"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="off"
                                spellcheck="false"
                                x-model="input"
                                x-on:keydown.enter.prevent="send()"
                                placeholder="{{ __('Message…') }}"
                            />
                            <button type="button" class="fi-ra-send" x-on:click="send()" :disabled="loading || !input.trim()">
                                {{ __('Send') }}
                            </button>
                        </div>
                        <p x-show="error" class="fi-ra-err" x-text="error"></p>
                    </div>
                </div>

                <button
                    type="button"
                    class="fi-ra-fab"
                    x-on:click="open = !open"
                    title="{{ __('Open assistant') }}"
                    x-bind:aria-expanded="open"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2L9.5 7.5h5L12 2z" fill="#FDE68A"/>
                        <path d="M5 9.5h14l-1.2 9.5H6.2L5 9.5z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" fill="rgba(255,255,255,0.15)"/>
                        <circle cx="9" cy="14" r="1.35" fill="currentColor"/>
                        <circle cx="15" cy="14" r="1.35" fill="currentColor"/>
                        <path d="M9.5 17.5c.8 1 2.2 1 3 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
