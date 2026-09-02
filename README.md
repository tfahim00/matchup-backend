# MatchUp Backend API

MatchUp is a Laravel-based API for a football/social match platform. The backend provides authentication, player profiles, team management, and match discovery/join flows for the app.

## Base URL

```txt
http://localhost:8000/api
```

If you are running the app on a different host or port, replace the base URL accordingly.

## Common API conventions

- All endpoints are under `/api`
- JSON responses are returned for API routes
- Authentication uses Laravel Sanctum
- Protected routes require the header:

```http
Authorization: Bearer <token>
```

- The API usually responds with:
  - `200 OK` for successful reads/updates
  - `201 Created` for resource creation
  - `401 Unauthorized` for invalid or missing auth
  - `403 Forbidden` for restricted actions
  - `404 Not Found` when the resource does not exist
  - `422 Unprocessable Entity` for validation failures

---

## Setup

```bash
cd matchup-backend
composer install
cp .env.example .env
php artisan key:generate
```

Then configure your database in `.env` and run:

```bash
php artisan migrate
php artisan serve
```

---

## Authentication APIs

### 1) Register a user

**Endpoint:** `POST /api/register`

**Request body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "role": "player"
}
```

**Optional role values:** `player`, `organizer`, `admin`

**Example:**

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "role": "player"
  }'
```

**Response:**

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "player"
  },
  "token": "<sanctum_token>",
  "token_type": "Bearer"
}
```

### 2) Login

**Endpoint:** `POST /api/login`

**Request body:**

```json
{
  "email": "john@example.com",
  "password": "Password123!"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "Password123!"
  }'
```

**Response:**

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "<sanctum_token>",
  "token_type": "Bearer"
}
```

### 3) Get current authenticated user

**Endpoint:** `GET /api/me`

**Auth required:** Yes

```bash
curl http://localhost:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 4) Logout

**Endpoint:** `POST /api/logout`

**Auth required:** Yes

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 5) Forgot password

**Endpoint:** `POST /api/forgot-password`

**Request body:**

```json
{
  "email": "john@example.com"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/forgot-password \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com"
  }'
```

**Response:**

```json
{
  "status": "reset-link-sent",
  "message": "We have emailed your password reset link!",
  "reset_link": "http://localhost:8000/reset-password/<token>?email=john@example.com"
}
```

### 6) Reset password

**Endpoint:** `POST /api/reset-password`

**Request body:**

```json
{
  "token": "<reset_token>",
  "email": "john@example.com",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/reset-password \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "<reset_token>",
    "email": "john@example.com",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
  }'
```

---

## Player Profile APIs

### 1) Create profile

**Endpoint:** `POST /api/profile`

**Auth required:** Yes

**Request body:**

```json
{
  "bio": "I am a striker who loves competitive football.",
  "preferred_position": "ST",
  "skill_level": "intermediate",
  "age": 25,
  "height": 176,
  "weight": 70,
  "dominant_foot": "right",
  "city": "Dhaka",
  "district": "Dhanmondi"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/profile \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "bio": "I am a striker who loves competitive football.",
    "preferred_position": "ST",
    "skill_level": "intermediate",
    "age": 25,
    "height": 176,
    "weight": 70,
    "dominant_foot": "right",
    "city": "Dhaka",
    "district": "Dhanmondi"
  }'
```

### 2) View authenticated user's profile

**Endpoint:** `GET /api/profile`

**Auth required:** Yes

```bash
curl http://localhost:8000/api/profile \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 3) Update profile

**Endpoint:** `PUT /api/profile`

**Auth required:** Yes

**Example:**

```bash
curl -X PUT http://localhost:8000/api/profile \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "bio": "Updated bio",
    "preferred_position": "CM",
    "skill_level": "advanced",
    "city": "Chattogram"
  }'
```

### 4) View public player profile

**Endpoint:** `GET /api/players/{id}`

**Auth required:** No

```bash
curl http://localhost:8000/api/players/1 \
  -H "Accept: application/json"
```

---

## Team APIs

### 1) List teams

**Endpoint:** `GET /api/teams`

**Auth required:** No

```bash
curl http://localhost:8000/api/teams \
  -H "Accept: application/json"
```

### 2) Create team

**Endpoint:** `POST /api/teams`

**Auth required:** Yes

**Request body:**

```json
{
  "name": "Blue Strikers",
  "description": "A competitive local team",
  "city": "Dhaka",
  "district": "Bashundhara",
  "visibility": "public"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/teams \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Blue Strikers",
    "description": "A competitive local team",
    "city": "Dhaka",
    "district": "Bashundhara",
    "visibility": "public"
  }'
```

### 3) Show a single team

**Endpoint:** `GET /api/teams/{team}`

```bash
curl http://localhost:8000/api/teams/1 \
  -H "Accept: application/json"
```

### 4) Update team

**Endpoint:** `PUT /api/teams/{team}`

**Auth required:** Yes, only team owner can update it.

```bash
curl -X PUT http://localhost:8000/api/teams/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Blue Strikers FC",
    "visibility": "private"
  }'
```

### 5) Delete team

**Endpoint:** `DELETE /api/teams/{team}`

**Auth required:** Yes, only team owner can delete it.

```bash
curl -X DELETE http://localhost:8000/api/teams/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 6) Get team members

**Endpoint:** `GET /api/teams/{team}/members`

```bash
curl http://localhost:8000/api/teams/1/members \
  -H "Accept: application/json"
```

### 7) Add member to team

**Endpoint:** `POST /api/teams/{team}/members`

**Auth required:** Yes, only team owner can add members.

**Request body:**

```json
{
  "user_id": 2,
  "role": "player",
  "status": "active"
}
```

### 8) Remove member from team

**Endpoint:** `DELETE /api/teams/{team}/members/{member}`

**Auth required:** Yes, team owner or self-removal is allowed.

---

## Match APIs

### 1) List matches

**Endpoint:** `GET /api/matches`

**Auth required:** No

**Optional query params:**

- `lat`: latitude of the user's location
- `lng`: longitude of the user's location
- `radius`: search radius in kilometers (default: `10`)

**Example nearby search:**

```bash
curl "http://localhost:8000/api/matches?lat=23.8103&lng=90.4125&radius=10" \
  -H "Accept: application/json"
```

When `lat` and `lng` are provided, each result includes a `distance` field in kilometers.

### 2) Create match

**Endpoint:** `POST /api/matches`

**Auth required:** Yes

**Request body:**

```json
{
  "title": "Weekend 5v5 Match",
  "description": "Friendly game at the local field.",
  "location_id": 1,
  "skill_level": "mixed",
  "match_type": "5v5",
  "slots_available": 10,
  "match_date": "2026-09-15 18:00:00",
  "status": "open",
  "visibility": "public"
}
```

**Example:**

```bash
curl -X POST http://localhost:8000/api/matches \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Weekend 5v5 Match",
    "description": "Friendly game at the local field.",
    "location_id": 1,
    "skill_level": "mixed",
    "match_type": "5v5",
    "slots_available": 10,
    "match_date": "2026-09-15 18:00:00",
    "status": "open",
    "visibility": "public"
  }'
```

### 3) Show a match

**Endpoint:** `GET /api/matches/{match}`

```bash
curl http://localhost:8000/api/matches/1 \
  -H "Accept: application/json"
```

### 4) Join a match

**Endpoint:** `POST /api/matches/{match}/join`

**Auth required:** Yes

```bash
curl -X POST http://localhost:8000/api/matches/1/join \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 5) Leave a match

**Endpoint:** `POST /api/matches/{match}/leave`

**Auth required:** Yes

```bash
curl -X POST http://localhost:8000/api/matches/1/leave \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

## Location APIs

### 1) Search locations (public)

**Endpoint:** `GET /api/locations`

**Query params:**
- `q` — text query (name, address, city, district)
- `lat` — latitude (optional for nearby search)
- `lng` — longitude (optional for nearby search)
- `radius` — radius in kilometers (default: `5`) when `lat`+`lng` provided
- `per_page` — pagination size (default: `15`)

**Example (nearby):**

```bash
curl "http://localhost:8000/api/locations?lat=23.8103&lng=90.4125&radius=5" \
  -H "Accept: application/json"
```

When `lat` and `lng` are provided, each result includes a `distance` field (in km).

### 2) Create a location (auth required)

**Endpoint:** `POST /api/locations`

**Auth required:** Yes (`Authorization: Bearer <token>`)

**Request body:**

```json
{
  "name": "X Ground",
  "address": "123 Field St",
  "city": "Dhaka",
  "district": "Dhanmondi",
  "latitude": 23.7806,
  "longitude": 90.4076
}
```

Latitude and longitude are optional but must be provided together.

**Example:**

```bash
curl -X POST http://localhost:8000/api/locations \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"name":"X Ground","address":"123 Field St","city":"Dhaka","latitude":23.7806,"longitude":90.4076}'
```


---

## Response format example

```json
{
  "success": true,
  "message": "Profile created successfully",
  "data": {
    "id": 1,
    "user_id": 2,
    "bio": "Friendly football player",
    "preferred_position": "ST",
    "skill_level": "intermediate",
    "city": "Dhaka"
  }
}
```

Most controllers return JSON with either a resource object or a collection, and some endpoints also include a `success` and `message` wrapper.

---

## Notes

- This project is designed as an API-first backend for a football/social match platform.
- The application currently includes authentication, profile management, team management, and match discovery/join logic.
- The reset-password flow is also included through the public web route and API reset endpoint.

---

## Useful commands

```bash
php artisan serve
php artisan migrate
php artisan migrate:fresh --seed
php artisan test
```

---

## License

This project is for the MatchUp backend and is intended for the application’s internal development workflow.
