# Yoshida Motors — Car Appraisal Backend

Backend API for the used car appraisal application by **Yoshida Motors**. The system allows users to submit vehicle appraisal requests, upload photos, and receive price assessments from an admin.

Built with **Laravel** using **Firebase Authentication** for identity management and **Laravel Sanctum** for API tokens.

---

## Tech Stack

| Component          | Technology                           |
| ------------------ | ------------------------------------ |
| Framework          | Laravel 12                           |
| Authentication     | Firebase Admin SDK + Laravel Sanctum |
| Database           | MySQL / PostgreSQL                   |
| File Storage       | Laravel Storage (local/S3)           |
| Push Notifications | Firebase Cloud Messaging (FCM)       |
| Testing            | Pest                                 |

---

## Features

- **Multi-method authentication** via Firebase: Phone OTP, Email & Password, Google Sign-In
- **User profile** management
- **Appraisal Requests** — create, edit, delete, and submit vehicle appraisal requests
- **Vehicle photo uploads** per category (Front View, Rear View, Interior, Dashboard, etc.)
- **Appraisal status tracking**: `draft` → `submitted` → `under_review` → `completed` / `rejected`
- **Cursor-based pagination** for optimal performance on large datasets
- **Role-based access** (`user` / `admin`)

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / PostgreSQL
- Firebase Project (with Phone Auth, Email Auth, and/or Google Auth enabled)
- Firebase Admin SDK credentials file (`serviceAccountKey.json`)

---

## Installation

### 1. Clone & Install Dependencies

```bash
git clone <repository-url>
cd car_rongsok

composer install
npm install
```

Or use the built-in setup script:

```bash
composer run setup
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and fill in the required values:

```env
APP_NAME="Yoshida Motors"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yoshida_motors
DB_USERNAME=root
DB_PASSWORD=

# Firebase — path to the service account JSON file
FIREBASE_CREDENTIALS=storage/app/firebase/serviceAccountKey.json
```

### 3. Place the Firebase Service Account File

Download `serviceAccountKey.json` from your Firebase Console and place it at:

```
storage/app/firebase/serviceAccountKey.json
```

### 4. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

### 5. Create Storage Symlink

```bash
php artisan storage:link
```

### 6. Start the Server

```bash
php artisan serve
```

The server runs at `http://localhost:8000`.

---

## API Overview

Base URL: `http://your-domain.com/api/v1`

Headers required for authenticated requests:

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Authentication

| Method | Endpoint               | Description                              | Auth |
| ------ | ---------------------- | ---------------------------------------- | ---- |
| POST   | `/auth/register`       | Register via Phone OTP                   | No   |
| POST   | `/auth/login`          | Login via Phone OTP                      | No   |
| POST   | `/auth/register/email` | Register via Email & Password            | No   |
| POST   | `/auth/login/email`    | Login via Email & Password               | No   |
| POST   | `/auth/login/google`   | Login / auto-register via Google Sign-In | No   |
| GET    | `/auth/profile`        | Get current user profile                 | Yes  |
| PUT    | `/auth/profile`        | Update current user profile              | Yes  |
| POST   | `/auth/logout`         | Logout and revoke token                  | Yes  |

### Appraisal

| Method | Endpoint                                     | Description                          | Auth |
| ------ | -------------------------------------------- | ------------------------------------ | ---- |
| GET    | `/appraisals`                                | List appraisals (pagination, filter) | Yes  |
| POST   | `/appraisals`                                | Create a new appraisal request       | Yes  |
| GET    | `/appraisals/{id}`                           | Get appraisal detail                 | Yes  |
| PUT    | `/appraisals/{id}`                           | Update appraisal (only when `draft`) | Yes  |
| DELETE | `/appraisals/{id}`                           | Delete appraisal (only when `draft`) | Yes  |
| POST   | `/appraisals/{id}/submit`                    | Submit appraisal for review          | Yes  |
| POST   | `/appraisals/{id}/photos`                    | Upload a vehicle photo               | Yes  |
| DELETE | `/appraisals/{appraisalId}/photos/{photoId}` | Delete a photo                       | Yes  |

Full endpoint documentation with request/response examples is available in [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

---

## Firebase Authentication Flow

All authentication methods follow the same pattern:

1. **Frontend** performs the auth process on Firebase (OTP, email/password, or Google).
2. **Frontend** retrieves the **Firebase ID Token** via `result.user.getIdToken()`.
3. **Frontend** sends the ID Token to the corresponding backend endpoint.
4. **Backend** verifies the ID Token using the Firebase Admin SDK.
5. **Backend** returns a **Sanctum token** to be used for all subsequent requests.

> Sanctum tokens are stateless and remain valid until the user logs out.

---

## Appraisal Status

| Status         | Description                                              |
| -------------- | -------------------------------------------------------- |
| `draft`        | Newly created; can still be edited and deleted           |
| `submitted`    | Submitted and awaiting admin review                      |
| `under_review` | Currently being reviewed by an admin                     |
| `completed`    | Review complete; `final_price` and `admin_notes` are set |
| `rejected`     | Rejected by admin; see `admin_notes` for the reason      |

---

## Response Format

All responses follow a standard structure:

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

Error response:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {}
}
```

---

## Running Tests

```bash
composer run test
```

---

## License

Proprietary — Yoshida Motors. All rights reserved.
