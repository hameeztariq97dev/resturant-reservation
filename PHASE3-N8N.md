# Phase 3 — n8n AI agent + Filament chat

This phase connects **Google Gemini** (via n8n’s **AI Agent** node) to your Laravel **REST API** (`POST /api/reservations`, `DELETE /api/reservations/{code}`). Staff use a **gradient “AI” button** in the Filament **sidebar footer**; the chat calls Laravel, which **proxies** requests to n8n (avoids browser CORS issues).

---

## 1. Prerequisites

- **Phase 1 & 2** working: Laravel at `http://127.0.0.1:8000`, API as documented in `API.md`.
- **Node.js** 18+ (for n8n).
- **Google Gemini API key** (free tier): [Google AI Studio](https://aistudio.google.com/app/apikey).

---

## 2. Install and run n8n

```bash
npm install -g n8n
n8n start
```

Open **http://localhost:5678** (or **http://127.0.0.1:5678**).

Use a recent n8n **1.50+** (required for AI Agent / LangChain nodes).

---

## 3. Import the workflow (JSON)

1. In n8n: **Workflows → Import from File**.
2. Choose:  
   `n8n/reservation-agent-workflow.json`
3. After import:
   - Open the **Google Gemini Chat Model** sub-node.
   - **Credentials → Create new** → **Google Gemini (PaLM) API** (or your n8n’s equivalent name) and paste your API key.
   - Save the credential and attach it to the node.
4. If **HTTP Request Tool** nodes show as deprecated or missing: delete them and add **two** new **HTTP Request** nodes in **Tool** mode (see n8n docs: *HTTP Request → Options → Use as AI tool*), matching the URLs and JSON bodies in the JSON file, or rebuild from section 5 below.

---

## 4. Workflow behaviour (summary)

| Piece | Purpose |
|--------|---------|
| **Webhook** | `POST` path `reservation-agent` → production URL `http://127.0.0.1:5678/webhook/reservation-agent` |
| **AI Agent** | Tools agent; user text from `{{ $json.body.user_message }}` (with fallback to `user_message`) |
| **Gemini** | Model `models/gemini-1.5-flash` (change in the node if your account lists a different id) |
| **create_reservation** | `POST http://127.0.0.1:8000/api/reservations` with placeholders `name`, `email`, `phone`, `party_size`, `preferred_date` |
| **cancel_reservation** | `DELETE http://127.0.0.1:8000/api/reservations/{confirmation_code}` |

**Response mode:** `Last Node` — the AI Agent is the last node on the main branch; no separate “Respond to Webhook” node.

---

## 5. Manual setup (if you prefer not to import)

1. **Webhook** — Method `POST`, path `reservation-agent`, **Response** = `Last Node`.
2. **AI Agent** — Prompt: define user message as  
   `={{ $json.body.user_message }}`  
   (or expression with fallback to `$json.user_message`).
3. **System message** (under Options): include the assistant rules, tool names `create_reservation` and `cancel_reservation`, and today’s date, e.g.  
   `={{ "…Today is: " + $now.toISODate() }}`
4. **Google Gemini Chat Model** — connect to the agent’s **Chat Model** input; model `gemini-1.5-flash` / `models/gemini-1.5-flash` per your n8n UI.
5. **HTTP Request Tool** (×2) — **create** and **cancel** as in the spec; use **placeholder** syntax `{name}`, `{confirmation_code}`, etc., and define placeholders in the node so the LLM fills them (see imported JSON).
6. Connect **Webhook → AI Agent** (main). Connect **Gemini → AI Agent** (language model). Connect **both tools → AI Agent** (tool inputs).

---

## 6. Activate and test the webhook

1. **Activate** the workflow (toggle in n8n).
2. **Postman** (or curl):

```http
POST http://127.0.0.1:5678/webhook/reservation-agent
Content-Type: application/json

{
  "user_message": "I want to make a reservation for 3 people on April 5th"
}
```

Expect the agent to ask for missing fields, then call your Laravel API when it has **name, email, phone, party size, preferred_date (YYYY-MM-DD)**.

**Cancel test:**

```json
{
  "user_message": "Cancel my booking, code is ABCD1234"
}
```

Use a real **confirmation code** from your database.

---

## 7. Laravel configuration (Filament chat proxy)

The admin UI does **not** call n8n from the browser. It posts to Laravel, which POSTs to n8n.

1. In `.env`:

```env
N8N_WEBHOOK_URL=http://127.0.0.1:5678/webhook/reservation-agent
```

Use the **production** webhook URL shown in n8n when the workflow is **active** (copy from the Webhook node).

2. Config cache (if you use it): `php artisan config:clear`.

3. **Requirement:** Laravel’s server must be able to **HTTP POST** to that URL. If n8n runs in Docker and Laravel on the host, use `http://host.docker.internal:5678/...` or your LAN IP instead of `127.0.0.1`.

---

## 8. Filament UI

- Log in to **http://127.0.0.1:8000/admin**.
- Use the **floating assistant button** in the **bottom-right** corner of the screen.
- A chat panel opens above the button; messages go to `POST /admin/n8n-chat` → n8n.

Route name: `filament.admin.n8n-chat` (panel id `admin`).

---

## 9. Test scenarios (end-to-end)

| Scenario | Input (chat or Postman body) | Expected |
|----------|-------------------------------|----------|
| **Create** | “Book for 3 on April 5th” then provide name, email, phone when asked | **create_reservation** runs → JSON with `confirmation_code` from Laravel |
| **Cancel** | “Cancel reservation, code XXXXXXXX” | **cancel_reservation** runs → success message from API |

---

## 10. Troubleshooting

| Issue | What to check |
|--------|----------------|
| **503** from `/admin/n8n-chat` | `N8N_WEBHOOK_URL` missing or empty in `.env`. |
| **502** / connection errors | n8n not running; wrong URL; firewall; Docker networking (see §7). |
| **Gemini errors** | API key, model name, quota on Google AI Studio. |
| **Tool never called** | System prompt must mention tool names; ensure Tools Agent + Gemini model supports tool calling. |
| **Validation errors from Laravel** | `preferred_date` must be `Y-m-d` and not before today; `party_size` integer ≥ 1. |
| **n8n returns `{"message":"Error in workflow"}` (HTTP 500)** | Open **n8n → Executions**, click the failed run, and read the **red node** error. Most common: **Google Gemini credentials** not attached (or invalid key) on the **Google Gemini Chat Model** node; workflow not **Active**; **AI Agent** node version mismatch after import (upgrade in the editor). **Docker:** n8n cannot reach Laravel at `127.0.0.1:8000` — set HTTP tool URLs to `http://host.docker.internal:8000` (Windows/Mac Docker Desktop) or your host LAN IP. |
| **Re-import workflow** | After pulling updates, re-import `n8n/reservation-agent-workflow.json` or add the **Normalize input** Set node manually: map `user_message` from `$json.body.user_message` or `$json.user_message`, then feed **AI Agent** prompt with `{{ $json.user_message }}`. |

---

## 11. Files added in this repo

| File | Role |
|------|------|
| `n8n/reservation-agent-workflow.json` | Importable n8n workflow |
| `app/Http/Controllers/N8nChatController.php` | Proxies chat to n8n |
| `resources/views/filament/hooks/reservation-ai-chat.blade.php` | Bottom-right FAB + chat panel (render hook `BODY_END`) |
| `config/services.php` | `services.n8n.webhook_url` |

API contract remains unchanged; see **`API.md`**.
