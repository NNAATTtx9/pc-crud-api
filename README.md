# Charity Run Event – Registration & Booth Wallet API (Module A)

A **Laravel 12 REST API** for a charity run event, with a small admin web page for managing booth staff.

What the project does:

- **Runners** see the event and its run categories, then **register** by choosing a category and making a donation.
- Each runner gets a unique **6-character QR code** and a **credit wallet**. The starting credits come from the run category.
- **Booth staff** (game, food and merchandise booths) log in, **scan** a runner's QR code and **redeem** 10 credits. A runner can redeem only once at each booth type.
- **Admins** log in to **list and search registrants** and mark their **goodie bag** as collected.
- An **admin web page** (Blade + Bootstrap) has full **CRUD** for booth staff accounts.

---

## Tech Stack

| Part | Used |
|------|------|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL (XAMPP), database `01_module_a` |
| Auth | Custom bearer tokens (`api_token` table) + middleware `auth.admin` / `auth.booth` |
| Web UI | Blade templates + Bootstrap |
| Response format | JSON |

---

## Setup

```bash
composer install
cp .env.example .env          # set DB_DATABASE=01_module_a, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate
```

> The project has no seeder for event data. Before you test, add an **event** (`is_active = 1`), some **run categories**, an **admin** and a **booth staff** account (passwords hashed with `Hash::make`). You can also create booth staff from the admin web page.

The project runs from the XAMPP `htdocs` folder (there is an `index.php` and `.htaccess` in the root):

```
Base URL:  http://localhost/01_module_a/api
Web admin: http://localhost/01_module_a/admin/booth-staff
```

Send `Accept: application/json` on every API request. Protected routes also need:

```
Authorization: Bearer <token>
```

---

## Database Tables

| Table | Purpose | Key columns |
|-------|---------|-------------|
| `event` | The run event | name, tagline, event_date, location, description, banner_image, is_active |
| `run_category` | Run types (e.g. 5 km, 10 km) | event_id, slug, name, price, **credits**, is_active |
| `registrant` | Registered runners | category_id, name, email, mobile, tshirt_size, donation_amount, **qr_code** (unique, 6 chars), **credit_balance**, wallet_status (`active`/`exhausted`), goodie_status (`pending`/`collected`), pdpa_consent |
| `admin` | Admin accounts | username, password (hashed), role (`admin`/`super-admin`) |
| `booth_staff` | Booth staff accounts | username, password (hashed), booth_type (`game`/`food`/`merchandise`), is_active |
| `redemption` | Credit redemption log | registrant_id, booth_staff_id, booth_type, amount, balance_before, balance_after, redeemed_at — **unique (registrant_id, booth_type)** |
| `api_token` | Login tokens | token (60 chars), owner_type (`admin`/`booth`), owner_id, expires_at (admin 24 h, booth 8 h) |

---

## API Endpoints Overview

| # | Method | Endpoint | Access | Description |
|---|--------|----------|--------|-------------|
| 1 | POST | `/api/admin/login` | Public | Admin login, returns a token |
| 2 | POST | `/api/booth/login` | Public | Booth staff login, returns a token |
| 3 | GET | `/api/event` | Public | Get the active event |
| 4 | GET | `/api/categories` | Public | List active run categories |
| 5 | POST | `/api/register` | Public | Register a runner |
| 6 | GET | `/api/registrants/{id}` | Public | Get a registrant's public details |
| 7 | POST | `/api/booth/scan` | Booth token | Scan a QR code and check the wallet |
| 8 | POST | `/api/booth/redeem` | Booth token | Redeem 10 credits at this booth |
| 9 | GET | `/api/admin/registrants` | Admin token | List / search registrants (paginated) |
| 10 | PATCH | `/api/admin/registrants/{id}/goodie` | Admin token | Update goodie bag status |

> The response examples below use **sample data**. Your IDs, names, codes and dates will be different.

---

## 1. Authentication

### POST `/api/admin/login`

**Request**
```json
{ "username": "admin1", "password": "adminpass" }
```

**200 OK**
```json
{
  "token": "pY3kq9V0d2Lx...60 characters...",
  "admin": { "id": 1, "username": "admin1", "role": "admin" }
}
```

**401 Unauthorized** – wrong username or password
```json
{ "error": "Invalid credentials" }
```

### POST `/api/booth/login`

**Request**
```json
{ "username": "foodbooth1", "password": "password123" }
```

**200 OK**
```json
{
  "token": "Zx81mQ7cK0aB...60 characters...",
  "booth": { "id": 2, "username": "foodbooth1", "booth_type": "food" }
}
```

**401 Unauthorized** – wrong credentials **or** the booth account is inactive
```json
{ "error": "Invalid credentials" }
```

### Protected route errors (middleware)

| Case | Status | Response |
|------|--------|----------|
| No token / token not found | 401 | `{ "error": "Unauthenticated" }` |
| Booth token used on an admin route, or admin token on a booth route | 403 | `{ "error": "Forbidden" }` |
| Booth staff account deactivated | 401 | `{ "error": "Unauthenticated" }` |

---

## 2. Public Endpoints

### GET `/api/event`

**200 OK**
```json
{
  "id": 1,
  "name": "Run For Hope 2026",
  "tagline": "Every step counts",
  "event_date": "2026-12-12",
  "location": "East Coast Park",
  "description": "A charity fun run raising funds for children's education.",
  "banner_image": "banner.jpg"
}
```

**404 Not Found** – no active event
```json
{ "error": "Event not found" }
```

### GET `/api/categories`

Returns only **active** categories, sorted by **price** (lowest first).

**200 OK**
```json
[
  { "id": 1, "slug": "5k-fun-run", "name": "5km Fun Run", "description": "For beginners and families", "price": 20, "credits": 60 },
  { "id": 2, "slug": "10k-run",    "name": "10km Run",    "description": "For regular runners",        "price": 35, "credits": 60 }
]
```

### POST `/api/register`

**Request**
```json
{
  "category_id": 1,
  "name": "Tan Wei Ming",
  "email": "weiming@example.com",
  "mobile": "91234567",
  "tshirt_size": "M",
  "organisation": "ITE College",
  "donation_amount": 30,
  "pdpa_consent": true
}
```

**Validation rules**

| Field | Rule |
|-------|------|
| category_id | required, must be an **active** category |
| name | required, 2–100 characters |
| email | required, valid email |
| mobile | required, 8–20 characters |
| tshirt_size | required, one of `XS, S, M, L, XL, XXL` |
| organisation | optional |
| donation_amount | required, integer, **at least the category price** |
| pdpa_consent | must be accepted (`true` / `1` / `"yes"`) |

What the server does: it generates a unique QR code (6 characters, without confusing characters like `0/O/1/I`), sets `credit_balance` to the category's credits, `wallet_status = active` and `goodie_status = pending`.

**201 Created**
```json
{
  "id": 15,
  "name": "Tan Wei Ming",
  "qr_code": "K7M2QX",
  "category": {
    "id": 1, "event_id": 1, "slug": "5k-fun-run", "name": "5km Fun Run",
    "description": "For beginners and families", "price": 20, "credits": 60, "is_active": true
  },
  "donation_amount": 30,
  "credit_balance": 60
}
```

**422 Unprocessable Entity** – validation failed
```json
{
  "message": "The donation amount field must be at least 20. (and 1 more error)",
  "errors": {
    "donation_amount": ["The donation amount field must be at least 20."],
    "pdpa_consent": ["You must agree to the PDPA consent to register"]
  }
}
```

### GET `/api/registrants/{id}`

**200 OK**
```json
{
  "id": 15,
  "name": "Tan Wei Ming",
  "qr_code": "K7M2QX",
  "donation_amount": 30,
  "credit_balance": 60,
  "tshirt_size": "M",
  "registered_at": "2026-11-25 14:30:12",
  "category": { "id": 1, "name": "5km Fun Run" }
}
```

**404 Not Found**
```json
{ "error": "Registrant not found" }
```

---

## 3. Booth Staff Endpoints (Bearer booth token)

### POST `/api/booth/scan`

Checks a runner's wallet **without** spending credits. The QR code is not case-sensitive (`k7m2qx` works).

**Request**
```json
{ "qr_code": "K7M2QX" }
```

**200 OK**
```json
{
  "registrant": { "id": 15, "name": "Tan Wei Ming" },
  "credit_balance": 60,
  "wallet_status": "active",
  "has_redeemed_here": false
}
```

**404 Not Found**
```json
{ "error": "Invalid QR code" }
```

### POST `/api/booth/redeem`

Takes **10 credits** from the runner. This runs inside a **database transaction with a row lock**, so two booths scanning the same runner at the same moment can't double-spend. Each redemption is saved in the `redemption` table.

**Request**
```json
{ "qr_code": "K7M2QX" }
```

**200 OK**
```json
{
  "registrant": { "id": 15, "name": "Tan Wei Ming" },
  "amount": 10,
  "balance_before": 60,
  "balance_after": 50,
  "wallet_status": "active"
}
```

When the balance reaches **0**, `wallet_status` changes to `"exhausted"`.

**Error responses**

| Case | Status | Response |
|------|--------|----------|
| QR code not found | 404 | `{ "error": "Invalid QR code" }` |
| Runner already redeemed at this booth type | 409 | `{ "error": "Already redeemed at this booth" }` |
| Wallet is exhausted | 400 | `{ "error": "Wallet is exhausted" }` |
| Balance below 10 | 400 | `{ "error": "Insufficient balance" }` |

---

## 4. Admin Endpoints (Bearer admin token)

### GET `/api/admin/registrants?search=tan&perPage=10&page=1`

| Query | Default | Description |
|-------|---------|-------------|
| `search` | – | Matches **name or email** (partial match) |
| `perPage` | 10 | Rows per page (1–100) |
| `page` | 1 | Page number |

Newest registrations are listed first.

**200 OK**
```json
{
  "data": [
    {
      "id": 15,
      "name": "Tan Wei Ming",
      "email": "weiming@example.com",
      "mobile": "91234567",
      "tshirt_size": "M",
      "organisation": "ITE College",
      "donation_amount": 30,
      "qr_code": "K7M2QX",
      "credit_balance": 50,
      "wallet_status": "active",
      "goodie_status": "pending",
      "registered_at": "2026-11-25 14:30:12",
      "category": { "id": 1, "name": "5km Fun Run" }
    }
  ],
  "meta": { "page": 1, "per_page": 10, "total": 1, "last_page": 1 }
}
```

### PATCH `/api/admin/registrants/{id}/goodie`

**Request**
```json
{ "goodie_status": "collected" }
```
`goodie_status` must be `pending` or `collected`.

**200 OK** – returns the updated registrant (same format as one item in the list above)
```json
{
  "id": 15,
  "name": "Tan Wei Ming",
  "goodie_status": "collected",
  "...": "other admin fields",
  "category": { "id": 1, "name": "5km Fun Run" }
}
```

**404 Not Found**
```json
{ "error": "Registrant not found" }
```

**422** – `goodie_status` is not `pending` or `collected`.

---

## 5. Booth Staff CRUD (Admin Web Page)

A Blade + Bootstrap page for managing booth staff accounts.

| CRUD | Method | URL | What happens |
|------|--------|-----|--------------|
| **Read** | GET | `/admin/booth-staff` | Table of all booth staff: ID, username, booth type, **Active/Inactive** badge, Edit and Delete buttons |
| **Create** (form) | GET | `/admin/booth-staff/create` | Form: username, password, booth type (game / food / merchandise), active checkbox |
| **Create** (save) | POST | `/admin/booth-staff` | Validates, **hashes the password**, saves, redirects with *"Booth staff added"* |
| **Update** (form) | GET | `/admin/booth-staff/{id}/edit` | Edit form with the current values |
| **Update** (save) | POST | `/admin/booth-staff/{id}` | Changes booth type and active status; the password only changes if a new one is typed. Redirects with *"Booth staff updated"* |
| **Delete** | DELETE | `/admin/booth-staff/{id}` | Asks *"Delete this booth staff?"*, deletes, redirects with *"Booth staff deleted."* |

**Validation**

| Field | Create | Update |
|-------|--------|--------|
| username | required, 3–64 characters, **unique** | (not editable) |
| password | required, min 8 characters | optional, min 8 characters |
| booth_type | required: `game`, `food` or `merchandise` | required |
| is_active | checkbox | checkbox |

If the list is empty, the table shows *"No booth staff yet."* A booth staff member set to **Inactive** can no longer log in to the booth API.

---

## Status Codes Used

| Code | Meaning in this API |
|------|---------------------|
| 200 | Success |
| 201 | Registrant created |
| 400 | Wallet exhausted / insufficient balance |
| 401 | Invalid credentials / not logged in |
| 403 | Wrong token type for this route |
| 404 | Event, registrant or QR code not found |
| 409 | Already redeemed at this booth type |
| 422 | Validation error |

---

## Project Structure (main files)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── EventController.php          # GET /event
│   │   │   ├── CategoryController.php       # GET /categories
│   │   │   ├── RegistrationController.php   # POST /register
│   │   │   ├── RegistrantController.php     # GET /registrants/{id}
│   │   │   ├── Admin/AuthController.php         # admin login
│   │   │   ├── Admin/RegistrantController.php   # list + goodie update
│   │   │   ├── Booth/AuthController.php         # booth login
│   │   │   ├── Booth/ScanController.php         # scan QR
│   │   │   └── Booth/RedeemController.php       # redeem credits
│   │   └── BoothStaffViewController.php     # booth staff CRUD (web)
│   ├── Middleware/AdminAuth.php, BoothAuth.php
│   └── Requests/RegisterRequest.php         # registration validation
├── Models/ Event, RunCategory, Registrant, Admin, BoothStaff, Redemption, ApiToken
└── Services/QrCodeGenerator.php             # unique 6-char QR codes
routes/api.php, routes/web.php
resources/views/booth-staff/ index, create, edit
```
