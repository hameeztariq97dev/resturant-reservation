{{-- Loaded after Filament panel styles; scoped to layout + reservations list. --}}
<style>
    /* Top bar: remove the extra ring/outline “line” (keep a soft shadow only) */
    .fi-topbar nav {
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
        box-shadow: 0 1px 3px 0 rgb(15 23 42 / 0.06);
    }

    html.dark .fi-topbar nav {
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.4);
    }

    /* Sidebar ↔ main content: single vertical edge only (no inset glow) */
    @media (min-width: 1024px) {
        .fi-layout .fi-main-sidebar.fi-sidebar {
            border-inline-end: 1px solid rgb(226 232 240);
        }
    }

    html.dark .fi-layout .fi-main-sidebar.fi-sidebar {
        border-inline-end-color: rgb(51 65 85);
    }

    /* Reservations list: calmer table surface + row hover */
    .fi-resource-reservations .fi-ta-content {
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background-color: rgb(255 255 255);
    }

    html.dark .fi-resource-reservations .fi-ta-content {
        border-color: rgb(51 65 85 / 0.85);
        background-color: rgb(15 23 42 / 0.35);
    }

    .fi-resource-reservations .fi-ta-table tbody tr {
        transition: background-color 0.12s ease;
    }

    .fi-resource-reservations .fi-ta-table tbody tr:hover td {
        background-color: rgb(248 250 252);
    }

    html.dark .fi-resource-reservations .fi-ta-table tbody tr:hover td {
        background-color: rgb(30 41 59 / 0.45);
    }

    .fi-resource-reservations .fi-ta-table thead th {
        font-weight: 600;
        letter-spacing: 0.01em;
    }
</style>
