# Nexium Reservations — REST API (Phase 2)

Base URL (local): `http://127.0.0.1:8000`

All JSON responses use this shape:

```json
{
  "success": true,
  "message": "...",
  "data": { }
}
```

Validation errors (`422`) and missing resources (`404`) for `/api/*` are formatted in `app/Exceptions/Handler.php` and wired in `bootstrap/app.php` (Laravel 11).

---

## 1. Create reservation

**`POST /api/reservations`**

**Headers**

| Header         | Value              |
|----------------|--------------------|
| `Content-Type` | `application/json` |
| `Accept`       | `application/json` |

**Body (JSON)**

| Field            | Type    | Rules |
|------------------|---------|--------|
| `name`           | string  | required, max 255 |
| `email`          | string  | required, email |
| `phone`          | string  | required |
| `party_size`     | integer | required, min 1 |
| `restaurant_id`  | integer | optional, must exist in `restaurants` |
| `preferred_date` | date    | required, `Y-m-d`, not before today |

**cURL**

```bash
curl -X POST "http://127.0.0.1:8000/api/reservations" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"Test Customer\",
    \"email\": \"test@example.com\",
    \"phone\": \"+92-300-0000000\",
    \"party_size\": 2,
    \"restaurant_id\": 1,
    \"preferred_date\": \"2026-03-30\"
  }"
```

**Success — `201 Created`**

```json
{
  "success": true,
  "message": "Reservation created successfully.",
  "data": {
    "confirmation_code": "ABCD1234",
    "customer_name": "Test Customer",
    "restaurant": "The Golden Fork",
    "table": "Table 1",
    "party_size": 2,
    "reservation_date": "2026-03-30T00:00:00+00:00",
    "status": "pending"
  }
}
```

**No table available — `422`**

```json
{
  "success": false,
  "message": "No available table found for the requested party size and date",
  "data": null
}
```

**Validation failed — `422`**

```json
{
  "success": false,
  "message": "Validation failed.",
  "data": {
    "email": ["The email field is required."]
  }
}
```

---

## 2. Cancel reservation

**`DELETE /api/reservations/{code}`**

Replace `{code}` with the 8-character `confirmation_code` (case-sensitive as stored).

**cURL**

```bash
curl -X DELETE "http://127.0.0.1:8000/api/reservations/ABCD1234" \
  -H "Accept: application/json"
```

**Success — `200 OK`**

```json
{
  "success": true,
  "message": "Reservation cancelled successfully.",
  "data": {
    "confirmation_code": "ABCD1234",
    "status": "cancelled"
  }
}
```

**Not found — `404`**

```json
{
  "success": false,
  "message": "Reservation not found.",
  "data": null
}
```

**Already cancelled — `422`**

```json
{
  "success": false,
  "message": "Reservation is already cancelled.",
  "data": null
}
```

---

## Postman

1. **Create:** New request → Method **POST** → URL `http://127.0.0.1:8000/api/reservations` → Body → **raw** → **JSON** → paste the JSON from the create example (adjust `preferred_date` and `restaurant_id`).
2. **Cancel:** New request → Method **DELETE** → URL `http://127.0.0.1:8000/api/reservations/YOURCODE` → Send.

No authentication headers are required for these routes.
