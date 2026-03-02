# API Documentation - Car Rongsok

Dokumentasi lengkap untuk integrasi frontend dengan backend API.

## Base URL
```
http://your-domain.com/api/v1
```

## Headers
Semua request yang memerlukan autentikasi harus menyertakan header:
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

---

## � Firebase Authentication - Penjelasan Penting

### User Experience Flow
1. ✅ User **hanya input nomor telepon** (+628xxx)
2. ✅ Firebase **otomatis kirim OTP** via SMS
3. ✅ User **input kode OTP** (6 digit)
4. ✅ Selesai! User berhasil login/register

### Technical Flow (Behind the Scenes)
**Yang User Lihat:**
- Input nomor telepon → Input OTP → Done!

**Yang Terjadi di Backend:**
1. Frontend: `signInWithPhoneNumber()` → Firebase kirim OTP
2. Frontend: `confirmationResult.confirm(otp)` → Firebase verify OTP
3. Frontend: `result.user.getIdToken()` → **Dapat ID Token**
4. Frontend → Backend: Kirim **ID Token** + data
5. Backend: Verify **ID Token** → Extract **Firebase UID** → Register/Login

### ⚠️ Penting: ID Token vs Firebase UID

| Istilah          | Apa Itu?                                             | Kapan Dipakai?                                           |
| ---------------- | ---------------------------------------------------- | -------------------------------------------------------- |
| **ID Token**     | JWT token panjang dari Firebase setelah OTP berhasil | Dikirim dari **Frontend → Backend** untuk verifikasi     |
| **Firebase UID** | User ID unik dari Firebase (contoh: `abc123xyz`)     | Disimpan di **database backend** untuk identifikasi user |

**Request ke Backend:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1Ni...", // ← Ini yang dikirim (JWT panjang)
  "phone_number": "+628123456789"
}
```

**Backend Extract UID dari ID Token:**
```php
$verifiedToken = $firebaseAuth->verifyIdToken($request->id_token);
$firebaseUid = $verifiedToken->claims()->get('sub'); // ← "abc123xyz"
// Simpan $firebaseUid ke database
```

---

## 📱 Authentication Endpoints

> Backend selalu menggunakan `verifyIdToken()` dari Firebase Admin SDK untuk semua metode auth.
> Yang berbeda hanya cara frontend mendapatkan ID Token tersebut.

| Metode           | Endpoint                    | Keterangan                                                       |
| ---------------- | --------------------------- | ---------------------------------------------------------------- |
| Phone OTP        | `POST /auth/phone`          | **Recommended** — combined login/register, returns `is_new_user` |
| Phone OTP        | `POST /auth/register`       | Legacy — register only (error jika user sudah ada)               |
| Phone OTP        | `POST /auth/login`          | Legacy — login only (error jika user belum ada)                  |
| Email & Password | `POST /auth/register/email` | Register dengan email/password                                   |
| Email & Password | `POST /auth/login/email`    | Login dengan email/password                                      |
| Google Sign-In   | `POST /auth/login/google`   | Combined login/register, returns `is_new_user`                   |

---

### 1. Login or Register with Phone OTP (Combined — Recommended)
**Endpoint:** `POST /auth/phone`
**Auth Required:** No

> **Endpoint ini menggabungkan register & login dalam satu hit.** Digunakan oleh `register_screen.dart` setelah OTP berhasil diverifikasi.
> - Jika user belum ada → otomatis register (`is_new_user: true`) → navigasi ke `profile_screen`
> - Jika sudah ada → login (`is_new_user: false`) → navigasi ke `home_screen`

**Flow yang Terjadi:**
1. ✅ User input **nomor telepon** di app
2. ✅ Firebase kirim **OTP** ke nomor telepon
3. ✅ User input **kode OTP**
4. ✅ Firebase verify OTP → dapat **ID Token**
5. ✅ Frontend kirim **ID Token** ke endpoint ini
6. ✅ Backend verify ID Token → cari user by UID → login atau auto-register

**Request Body:**
```json
{
  "id_token": "string (required) - ID Token dari Firebase setelah OTP berhasil",
  "phone_number": "string (optional) - Nomor telepon format E.164 (+628xxx); diambil dari token jika tersedia",
  "fcm_token": "string (optional) - Firebase Cloud Messaging token untuk push notif"
}
```

**Example:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjFlOWdkazcifQ...",
  "phone_number": "+628123456789",
  "fcm_token": "fcm_device_token_here"
}
```

**Success Response (200) - User Sudah Ada (Login):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "firebase_uid": "abc123xyz",
      "phone_number": "+628123456789",
      "name": "John Doe",
      "email": null,
      "address": "Jakarta Selatan",
      "gender": "male",
      "birth_date": "1990-01-01",
      "auth_provider": "phone",
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-09T10:00:00.000000Z",
      "updated_at": "2026-02-09T10:30:00.000000Z"
    },
    "token": "1|abc123tokenxyz...",
    "is_new_user": false
  }
}
```

**Success Response (201) - User Baru (Auto Register):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 5,
      "firebase_uid": "abc123xyz",
      "phone_number": "+628123456789",
      "name": null,
      "email": null,
      "address": null,
      "gender": null,
      "birth_date": null,
      "auth_provider": "phone",
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-19T10:00:00.000000Z",
      "updated_at": "2026-02-19T10:00:00.000000Z"
    },
    "token": "2|newtoken...",
    "is_new_user": true
  }
}
```

> Jika `is_new_user: true` → arahkan user ke `profile_screen` untuk mengisi nama & alamat.

**Error Response (401) - Firebase Verification Failed:**
```json
{
  "success": false,
  "message": "Firebase authentication failed: Invalid token",
  "errors": null
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "id_token": ["The id token field is required."]
  }
}
```

---

### 2. Register with Firebase (Phone OTP) — Legacy
**Endpoint:** `POST /auth/register`
**Auth Required:** No

**Flow yang Terjadi:**
1. ✅ User input **nomor telepon** di app
2. ✅ Firebase kirim **OTP** ke nomor telepon
3. ✅ User input **kode OTP**
4. ✅ Firebase verify OTP → dapat **ID Token**
5. ✅ Frontend kirim **ID Token** + data ke endpoint ini
6. ✅ Backend verify ID Token → register user

**Request Body:**
```json
{
  "id_token": "string (required) - ID Token dari Firebase setelah OTP berhasil",
  "phone_number": "string (required, unique) - Nomor telepon format E.164 (+628xxx)",
  "name": "string (optional, max:255)",
  "email": "string|email (optional, unique)",
  "address": "string (optional)",
  "gender": "enum (optional) - male, female, other",
  "birth_date": "date (optional) - YYYY-MM-DD",
  "fcm_token": "string (optional) - Firebase Cloud Messaging token untuk push notif"
}
```

**Example:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjFlOWdkazcifQ.ewogImlzcyI6ICJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20v...",
  "phone_number": "+628123456789",
  "name": "John Doe",
  "email": "john@example.com",
  "address": "Jakarta Selatan",
  "fcm_token": "fcm_device_token_here"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "firebase_uid": "abc123xyz",
      "phone_number": "+628123456789",
      "name": "John Doe",
      "email": "john@example.com",
      "address": "Jakarta Selatan",
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-09T10:00:00.000000Z",
      "updated_at": "2026-02-09T10:00:00.000000Z"
    },
    "token": "1|abc123tokenxyz..."
  }
}
```

**Error Response (409) - User Already Exists:**
```json
{
  "success": false,
  "message": "User already registered. Please login instead.",
  "errors": null
}
```

**Error Response (401) - Firebase Verification Failed:**
```json
{
  "success": false,
  "message": "Firebase authentication failed: Invalid token",
  "errors": null
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "id_token": ["The id token field is required."],
    "phone_number": ["The phone number field is required.", "The phone number has already been taken."],
    "email": ["The email has already been taken."]
  }
}
```

---

### 3. Login with Firebase (Phone OTP) — Legacy
**Endpoint:** `POST /auth/login`
**Auth Required:** No

**Flow yang Terjadi:**
1. ✅ User input **nomor telepon** di app (sama seperti register)
2. ✅ Firebase kirim **OTP** ke nomor telepon
3. ✅ User input **kode OTP**
4. ✅ Firebase verify OTP → dapat **ID Token**
5. ✅ Frontend kirim **ID Token** ke endpoint ini
6. ✅ Backend verify ID Token → login user

**Request Body:**
```json
{
  "id_token": "string (required) - ID Token dari Firebase setelah OTP berhasil",
  "fcm_token": "string (optional) - Update FCM token jika device berubah"
}
```

**Example:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6IjFlOWdkazcifQ.ewogImlzcyI6ICJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20v...",
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "firebase_uid": "abc123xyz",
      "phone_number": "+628123456789",
      "name": "John Doe",
      "email": "john@example.com",
      "address": "Jakarta Selatan",
      "role": "user",
      "fcm_token": "new_fcm_token_if_device_changed",
      "created_at": "2026-02-09T10:00:00.000000Z",
      "updated_at": "2026-02-09T10:30:00.000000Z"
    },
    "token": "1|new_access_token_xyz..."
  }
}
```

**Error Response (404) - User Not Found:**
```json
{
  "success": false,
  "message": "User not found. Please register first.",
  "errors": null
}
```

**Error Response (401) - Firebase Verification Failed:**
```json
{
  "success": false,
  "message": "Firebase authentication failed: Invalid token",
  "errors": null
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "id_token": ["The id token field is required."]
  }
}
```

---

### 4. Register with Email & Password
**Endpoint:** `POST /auth/register/email`
**Auth Required:** No

**Flow yang Terjadi:**
1. ✅ User input **email + password** di app
2. ✅ Frontend: Firebase `createUserWithEmailAndPassword(email, password)`
3. ✅ Frontend: `result.user.getIdToken()` → dapat **ID Token**
4. ✅ Frontend kirim **ID Token** + data user ke endpoint ini
5. ✅ Backend verify ID Token → extract UID + email → register user

**Request Body:**
```json
{
  "id_token": "string (required) - ID Token dari Firebase setelah createUserWithEmailAndPassword",
  "name": "string (optional, max:255)",
  "phone_number": "string (optional, unique)",
  "address": "string (optional)",
  "gender": "enum (optional) - male, female, other",
  "birth_date": "date (optional) - YYYY-MM-DD",
  "fcm_token": "string (optional)"
}
```

> ⚠️ Email diambil otomatis dari **claims ID Token** Firebase, tidak perlu dikirim ulang dari frontend.

**Example:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...",
  "name": "John Doe",
  "phone_number": "+628123456789",
  "address": "Jakarta Selatan",
  "fcm_token": "fcm_device_token_here"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 2,
      "firebase_uid": "def456xyz",
      "email": "john@example.com",
      "name": "John Doe",
      "phone_number": "+628123456789",
      "address": "Jakarta Selatan",
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-09T10:00:00.000000Z",
      "updated_at": "2026-02-09T10:00:00.000000Z"
    },
    "token": "2|abc123tokenxyz..."
  }
}
```

**Error Response (409) - User Already Exists:**
```json
{
  "success": false,
  "message": "User already registered. Please login instead.",
  "errors": null
}
```

**Error Response (422) - Email Not in Token:**
```json
{
  "success": false,
  "message": "Email not found in Firebase token.",
  "errors": null
}
```

---

### 5. Login with Email & Password
**Endpoint:** `POST /auth/login/email`
**Auth Required:** No

**Flow yang Terjadi:**
1. ✅ User input **email + password** di app
2. ✅ Frontend: Firebase `signInWithEmailAndPassword(email, password)`
3. ✅ Frontend: `result.user.getIdToken()` → dapat **ID Token**
4. ✅ Frontend kirim **ID Token** ke endpoint ini
5. ✅ Backend verify ID Token → extract UID → login user

**Request Body:**
```json
{
  "id_token": "string (required) - ID Token dari Firebase setelah signInWithEmailAndPassword",
  "fcm_token": "string (optional) - Update FCM token jika device berubah"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 2,
      "firebase_uid": "def456xyz",
      "email": "john@example.com",
      "name": "John Doe",
      "phone_number": "+628123456789",
      "address": "Jakarta Selatan",
      "role": "user",
      "fcm_token": "new_fcm_token_if_changed",
      "created_at": "2026-02-09T10:00:00.000000Z",
      "updated_at": "2026-02-09T10:30:00.000000Z"
    },
    "token": "3|new_access_token_xyz..."
  }
}
```

**Error Response (404) - User Not Found:**
```json
{
  "success": false,
  "message": "User not found. Please register first.",
  "errors": null
}
```

**Error Response (401) - Firebase Verification Failed:**
```json
{
  "success": false,
  "message": "Firebase authentication failed: Invalid token",
  "errors": null
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "id_token": ["The id token field is required."]
  }
}
```

---

### 6. Login / Register with Google Sign-In
**Endpoint:** `POST /auth/login/google`
**Auth Required:** No

> **Endpoint ini menggabungkan register & login dalam satu hit.**
> Jika user belum ada → otomatis register. Jika sudah ada → login.
> Gunakan field `is_new_user` di response untuk menentukan apakah perlu tampilkan onboarding.

**Flow yang Terjadi:**
1. ✅ User tap tombol **"Sign in with Google"**
2. ✅ Frontend: Google OAuth → `GoogleAuthProvider.credential(googleIdToken)`
3. ✅ Frontend: Firebase `signInWithCredential()` → dapat **Firebase ID Token**
4. ✅ Frontend kirim **Firebase ID Token** ke endpoint ini
5. ✅ Backend verify token → extract UID + email + name → auto register atau login

**Request Body:**
```json
{
  "id_token": "string (required) - Firebase ID Token (BUKAN Google ID Token langsung)",
  "fcm_token": "string (optional)"
}
```

> ⚠️ Yang dikirim adalah **Firebase ID Token** (dari `result.user.getIdToken()`), bukan raw Google ID Token.

**Example:**
```json
{
  "id_token": "eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...",
  "fcm_token": "fcm_device_token_here"
}
```

**Success Response (200) - User Sudah Ada (Login):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 3,
      "firebase_uid": "ghi789xyz",
      "email": "jane@gmail.com",
      "name": "Jane Doe",
      "phone_number": null,
      "address": null,
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-01T08:00:00.000000Z",
      "updated_at": "2026-02-09T10:00:00.000000Z"
    },
    "token": "4|google_login_token...",
    "is_new_user": false
  }
}
```

**Success Response (201) - User Baru (Auto Register):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 4,
      "firebase_uid": "jkl012xyz",
      "email": "newuser@gmail.com",
      "name": "New User",
      "phone_number": null,
      "address": null,
      "role": "user",
      "fcm_token": "fcm_device_token_here",
      "created_at": "2026-02-09T11:00:00.000000Z",
      "updated_at": "2026-02-09T11:00:00.000000Z"
    },
    "token": "5|google_new_token...",
    "is_new_user": true
  }
}
```

**Error Response (422) - Email Not in Token:**
```json
{
  "success": false,
  "message": "Email not found in Google token.",
  "errors": null
}
```

**Error Response (401) - Firebase Verification Failed:**
```json
{
  "success": false,
  "message": "Firebase authentication failed: Invalid token",
  "errors": null
}
```

---

### 7. Get User Profile
**Endpoint:** `GET /auth/profile`
**Auth Required:** Yes

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Profile retrieved successfully",
  "data": {
    "id": 1,
    "firebase_uid": "abc123xyz",
    "phone_number": "+628123456789",
    "name": "John Doe",
    "email": "john@example.com",
    "address": "Jakarta",
    "role": "user",
    "fcm_token": "fcm_token_here",
    "created_at": "2026-01-20T10:00:00.000000Z",
    "updated_at": "2026-01-20T10:00:00.000000Z"
  }
}
```

**Unauthorized Response (401):**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

---

### 8. Update User Profile
**Endpoint:** `PUT /auth/profile`
**Auth Required:** Yes

**Request Body:**
```json
{
  "name": "string (optional)",
  "phone_number": "string (optional) - Hanya bisa jika auth_provider bukan 'phone'",
  "email": "string|email (optional)",
  "address": "string (optional)",
  "gender": "enum (optional) - male, female, other",
  "birth_date": "date (optional) - YYYY-MM-DD",
  "fcm_token": "string (optional)"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "firebase_uid": "abc123xyz",
    "phone_number": "+628123456789",
    "name": "John Doe Updated",
    "email": "newemail@example.com",
    "address": "Bandung",
    "role": "user",
    "fcm_token": "new_fcm_token",
    "created_at": "2026-01-20T10:00:00.000000Z",
    "updated_at": "2026-02-09T12:00:00.000000Z"
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 9. Logout
**Endpoint:** `POST /auth/logout`
**Auth Required:** Yes

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout successful",
  "data": null
}
```

---

## 🚗 Appraisal Request Endpoints

### 1. Get All Appraisal Requests (with Pagination, Search, Filter, Sort)
**Endpoint:** `GET /appraisals`
**Auth Required:** Yes

**Query Parameters:**
```
search          : string (optional) - Search in brand, model, or description
status          : string (optional) - Filter by status: draft|submitted|under_review|completed|rejected
year_min        : integer (optional) - Minimum year manufacture
year_max        : integer (optional) - Maximum year manufacture
sort_by         : string (optional) - Sort field: created_at|updated_at|year_manufacture|vehicle_brand|final_price (default: created_at)
sort_dir        : string (optional) - Sort direction: asc|desc (default: desc)
per_page        : integer (optional) - Items per page (default: 10)
cursor          : string (optional) - Cursor for pagination
```

**Example Request:**
```
GET /appraisals?search=honda&status=draft&year_min=2020&sort_by=year_manufacture&sort_dir=desc&per_page=15
```

**Success Response (200) - Cursor Pagination:**
```json
{
  "success": true,
  "message": "Appraisal requests retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "vehicle_brand": "Toyota",
      "vehicle_model": "Avanza",
      "year_manufacture": 2020,
      "description": "Mobil terawat, service rutin",
      "license_plate": "B 5678 ABC",
      "mileage": 78000,
      "status": "draft",
      "final_price": null,
      "admin_note": null,
      "price_valid_until": null,
      "created_at": "2026-02-01T10:00:00.000000Z",
      "updated_at": "2026-02-01T10:00:00.000000Z",
      "photos": [
        {
          "id": 1,
          "appraisal_request_id": 1,
          "category_name": "Tampak Depan",
          "image_path": "appraisal_photos/1/photo1.jpg",
          "created_at": "2026-02-01T11:00:00.000000Z",
          "updated_at": "2026-02-01T11:00:00.000000Z"
        }
      ]
    }
  ],
  "meta": {
    "per_page": 15,
    "next_cursor": "eyJpZCI6MTUsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0",
    "prev_cursor": null,
    "has_more": true
  }
}
```

**Notes untuk Frontend:**
- Gunakan `next_cursor` untuk load more data berikutnya
- Kirim `cursor` parameter dengan value dari `next_cursor` untuk infinite scroll
- `has_more: true` berarti masih ada data selanjutnya
- `prev_cursor` bisa digunakan untuk navigasi mundur (opsional)

---

### 2. Get Latest Appraisal Request
**Endpoint:** `GET /appraisals/latest`
**Auth Required:** Yes

Digunakan oleh `home_screen.dart` untuk menampilkan status card appraisal aktif.

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Latest appraisal request retrieved successfully",
  "data": {
    "id": 5,
    "user_id": 1,
    "vehicle_brand": "Honda",
    "vehicle_model": "Jazz RS",
    "year_manufacture": 2022,
    "description": "Mobil terawat",
    "license_plate": "B 1234 XYZ",
    "mileage": 45000,
    "status": "submitted",
    "final_price": null,
    "admin_note": null,
    "price_valid_until": null,
    "created_at": "2026-02-09T15:00:00.000000Z",
    "updated_at": "2026-02-09T17:00:00.000000Z",
    "photos": [
      {
        "id": 10,
        "appraisal_request_id": 5,
        "category_name": "Tampak Depan",
        "image_path": "appraisal_photos/5/xyz123.jpg",
        "created_at": "2026-02-09T16:00:00.000000Z",
        "updated_at": "2026-02-09T16:00:00.000000Z"
      }
    ]
  }
}
```

**Not Found Response (404) - Belum Ada Appraisal:**
```json
{
  "success": false,
  "message": "No appraisal request found",
  "errors": null
}
```

---

### 3. Create New Appraisal Request
**Endpoint:** `POST /appraisals`
**Auth Required:** Yes
**Content-Type:** `multipart/form-data`

**Request Body (Form Data):**
```
vehicle_brand    : string (required, max:255)
vehicle_model    : string (required, max:255)
year_manufacture : integer (required, min:1900, max:2027)
description      : string (optional)
license_plate    : string (optional, max:20) - Nomor plat kendaraan
mileage          : integer (optional, min:0) - Jarak tempuh dalam km
photos[]         : array of files (optional, image: jpeg|png|jpg, max: 5MB per file)
photo_labels[]   : array of strings (optional) - Label untuk masing-masing foto sesuai urutan
```

**Example (JavaScript FormData):**
```javascript
const formData = new FormData();
formData.append('vehicle_brand', 'Honda');
formData.append('vehicle_model', 'Jazz RS');
formData.append('year_manufacture', '2022');
formData.append('description', 'Mobil masih sangat terawat, kilometer rendah');
formData.append('license_plate', 'B 1234 XYZ');
formData.append('mileage', '45000');

// Append multiple photos and their labels
formData.append('photos[]', fileObject1);
formData.append('photo_labels[]', 'Tampak Depan');

formData.append('photos[]', fileObject2);
formData.append('photo_labels[]', 'Tampak Belakang');
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Appraisal request created successfully",
  "data": {
    "id": 5,
    "user_id": 1,
    "vehicle_brand": "Honda",
    "vehicle_model": "Jazz RS",
    "year_manufacture": 2022,
    "description": "Mobil masih sangat terawat, kilometer rendah",
    "license_plate": "B 1234 XYZ",
    "mileage": 45000,
    "status": "draft",
    "final_price": null,
    "admin_note": null,
    "price_valid_until": null,
    "created_at": "2026-02-09T15:00:00.000000Z",
    "updated_at": "2026-02-09T15:00:00.000000Z"
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "vehicle_brand": ["The vehicle brand field is required."],
    "year_manufacture": ["The year manufacture must be at least 1900."]
  }
}
```

---

### 4. Get Single Appraisal Request
**Endpoint:** `GET /appraisals/{id}`
**Auth Required:** Yes

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Appraisal request retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "vehicle_brand": "Toyota",
    "vehicle_model": "Avanza",
    "year_manufacture": 2020,
    "description": "Mobil terawat",
    "license_plate": "B 5678 ABC",
    "mileage": 78000,
    "status": "draft",
    "final_price": null,
    "admin_note": null,
    "price_valid_until": null,
    "created_at": "2026-02-01T10:00:00.000000Z",
    "updated_at": "2026-02-01T10:00:00.000000Z",
    "photos": [
      {
        "id": 1,
        "appraisal_request_id": 1,
        "category_name": "Tampak Depan",
        "image_path": "appraisal_photos/1/photo1.jpg",
        "created_at": "2026-02-01T11:00:00.000000Z",
        "updated_at": "2026-02-01T11:00:00.000000Z"
      },
      {
        "id": 2,
        "appraisal_request_id": 1,
        "category_name": "Tampak Belakang",
        "image_path": "appraisal_photos/1/photo2.jpg",
        "created_at": "2026-02-01T11:05:00.000000Z",
        "updated_at": "2026-02-01T11:05:00.000000Z"
      }
    ]
  }
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Appraisal request not found",
  "errors": null
}
```

---

### 5. Update Appraisal Request
**Endpoint:** `PUT /appraisals/{id}`
**Auth Required:** Yes
**Content-Type:** `multipart/form-data` (Gunakan `_method=PUT` jika menggunakan form-data)

**⚠️ Important:** Hanya bisa update appraisal dengan status `draft`

**Request Body (Form Data):**
```
_method          : string (required if using multipart/form-data) - "PUT"
vehicle_brand    : string (optional, max:255)
vehicle_model    : string (optional, max:255)
year_manufacture : integer (optional, min:1900, max:2027)
description      : string (optional)
license_plate    : string (optional, max:20)
mileage          : integer (optional, min:0)
new_photos[]     : array of files (optional, image: jpeg|png|jpg, max: 5MB per file)
new_photo_labels[]: array of strings (optional) - Label untuk foto baru
delete_photos[]  : array of integers (optional) - ID foto yang ingin dihapus
```

**Example (JavaScript FormData):**
```javascript
const formData = new FormData();
formData.append('_method', 'PUT');
formData.append('vehicle_model', 'Jazz RS CVT');
formData.append('description', 'Updated description with more details');

// Add new photos
formData.append('new_photos[]', fileObject);
formData.append('new_photo_labels[]', 'Interior');

// Delete existing photos by ID
formData.append('delete_photos[]', '10');
formData.append('delete_photos[]', '11');
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Appraisal request updated successfully",
  "data": {
    "id": 5,
    "user_id": 1,
    "vehicle_brand": "Honda",
    "vehicle_model": "Jazz RS CVT",
    "year_manufacture": 2022,
    "description": "Updated description with more details",
    "license_plate": "B 1234 XYZ",
    "mileage": 45000,
    "status": "draft",
    "final_price": null,
    "admin_note": null,
    "price_valid_until": null,
    "created_at": "2026-02-09T15:00:00.000000Z",
    "updated_at": "2026-02-09T15:30:00.000000Z"
  }
}
```

**Error Response (403) - Already Submitted:**
```json
{
  "success": false,
  "message": "Cannot update submitted appraisal request",
  "errors": null
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Appraisal request not found",
  "errors": null
}
```

---

### 6. Submit Appraisal for Review
**Endpoint:** `POST /appraisals/{id}/submit`
**Auth Required:** Yes

**⚠️ Important:**
- Hanya bisa submit appraisal dengan status `draft`
- Minimal harus ada 1 foto sebelum submit

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Appraisal submitted successfully",
  "data": {
    "id": 5,
    "user_id": 1,
    "vehicle_brand": "Honda",
    "vehicle_model": "Jazz RS CVT",
    "year_manufacture": 2022,
    "description": "Mobil terawat",
    "license_plate": "B 1234 XYZ",
    "mileage": 45000,
    "status": "submitted",
    "final_price": null,
    "admin_note": null,
    "price_valid_until": null,
    "created_at": "2026-02-09T15:00:00.000000Z",
    "updated_at": "2026-02-09T17:00:00.000000Z",
    "photos": [
      {
        "id": 10,
        "appraisal_request_id": 5,
        "category_name": "Tampak Depan",
        "image_path": "appraisal_photos/5/xyz123.jpg",
        "created_at": "2026-02-09T16:00:00.000000Z",
        "updated_at": "2026-02-09T16:00:00.000000Z"
      }
    ]
  }
}
```

**Error Response (400) - No Photos:**
```json
{
  "success": false,
  "message": "Please upload at least one photo before submitting",
  "errors": null
}
```

**Error Response (400) - Already Submitted:**
```json
{
  "success": false,
  "message": "Appraisal already submitted",
  "errors": null
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Appraisal request not found",
  "errors": null
}
```

---

### 9. Delete Appraisal Request
**Endpoint:** `DELETE /appraisals/{id}`
**Auth Required:** Yes

**⚠️ Important:** Hanya bisa delete appraisal dengan status `draft`

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Appraisal request deleted successfully",
  "data": null
}
```

**Error Response (403) - Already Submitted:**
```json
{
  "success": false,
  "message": "Cannot delete submitted appraisal request",
  "errors": null
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Appraisal request not found",
  "errors": null
}
```

---

## � Notification Endpoints

### 1. Get All Notifications (with Pagination)
**Endpoint:** `GET /notifications`
**Auth Required:** Yes

**Query Parameters:**
```
per_page        : integer (optional) - Items per page (default: 15)
cursor          : string (optional) - Cursor for pagination
```

**Success Response (200) - Cursor Pagination:**
```json
{
  "success": true,
  "message": "Notifications retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "title": "Appraisal Submitted",
      "body": "Your appraisal request for Honda Jazz RS CVT has been submitted.",
      "data": {
        "appraisal_id": 5,
        "status": "submitted"
      },
      "is_read": false,
      "created_at": "2026-02-09T17:00:00.000000Z",
      "updated_at": "2026-02-09T17:00:00.000000Z"
    }
  ],
  "meta": {
    "per_page": 15,
    "next_cursor": "eyJpZCI6MSwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ",
    "prev_cursor": null,
    "has_more": false
  }
}
```

---

### 2. Mark Notification as Read
**Endpoint:** `PUT /notifications/{id}/mark-read`
**Auth Required:** Yes

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notification marked as read",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Appraisal Submitted",
    "body": "Your appraisal request for Honda Jazz RS CVT has been submitted.",
    "data": {
      "appraisal_id": 5,
      "status": "submitted"
    },
    "is_read": true,
    "created_at": "2026-02-09T17:00:00.000000Z",
    "updated_at": "2026-02-09T17:05:00.000000Z"
  }
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Notification not found",
  "errors": null
}
```

---

### 3. Mark All Notifications as Read
**Endpoint:** `PUT /notifications/mark-all-read`
**Auth Required:** Yes

**Request:** No body required

**Success Response (200):**
```json
{
  "success": true,
  "message": "All notifications marked as read",
  "data": null
}
```

---

## �📋 Standard Response Format

Semua response mengikuti format standar dari `ApiResponseTrait`:

### Success Response
```json
{
  "success": true,
  "message": "Success message here",
  "data": {} // or [] or null
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message here",
  "errors": {} // or null
}
```

### Cursor Paginated Response
```json
{
  "success": true,
  "message": "Success message",
  "data": [], // Array of items
  "meta": {
    "per_page": 10,
    "next_cursor": "encoded_cursor_string",
    "prev_cursor": null,
    "has_more": true
  }
}
```

---

## 🔐 Authentication Flow

### Phone OTP — Login / Auto Register (Recommended)
1. User input **nomor telepon** di aplikasi mobile
2. Firebase kirim **OTP** via SMS
3. User input **kode OTP**
4. Firebase verify OTP → dapat **ID Token**
5. Frontend kirim **ID Token** ke `POST /auth/phone`
6. Backend verify ID Token → cari user by UID → auto-register jika baru, login jika ada
7. Backend return `user` + `token` + `is_new_user`
8. Simpan `token` di secure storage
9. Jika `is_new_user: true` → navigasi ke `profile_screen`; jika `false` → navigasi ke `home_screen`

### Phone OTP — Register (Legacy)
1. User input **nomor telepon** di aplikasi mobile
2. Firebase kirim **OTP** via SMS
3. User input **kode OTP**
4. Firebase verify OTP → dapat **ID Token**
5. Frontend kirim **ID Token** + data profil ke `POST /auth/register`
6. Backend verify ID Token → extract Firebase UID → create user
7. Backend return `token` (Sanctum token)
8. Simpan `token` di secure storage

### Phone OTP — Login (Legacy)
1. User input **nomor telepon**
2. Firebase kirim **OTP** via SMS
3. User input **kode OTP**
4. Firebase verify OTP → dapat **ID Token**
5. Frontend kirim `id_token` ke `POST /auth/login`
6. Backend return `user` + `token` baru

### Email & Password — Register
1. User input **email + password**
2. Firebase `createUserWithEmailAndPassword()` → dapat **ID Token**
3. Frontend kirim `id_token` + data ke `POST /auth/register/email`
4. Backend extract email dari token claims → create user
5. Backend return `token`

### Email & Password — Login
1. User input **email + password**
2. Firebase `signInWithEmailAndPassword()` → dapat **ID Token**
3. Frontend kirim `id_token` ke `POST /auth/login/email`
4. Backend return `user` + `token`

### Google Sign-In — Login / Auto Register
1. User tap **"Sign in with Google"**
2. Google OAuth → `GoogleAuthProvider.credential(googleIdToken)`
3. Firebase `signInWithCredential()` → dapat **Firebase ID Token**
4. Frontend kirim `id_token` ke `POST /auth/login/google`
5. Backend auto-register jika user baru, atau login jika sudah ada
6. Backend return `user` + `token` + `is_new_user`
7. Jika `is_new_user: true` → tampilkan onboarding / isi profil lengkap

### Authenticated Requests
- Untuk semua request berikutnya, kirim token di header: `Authorization: Bearer {token}`
- Token Sanctum bersifat stateless, tidak perlu refresh token
- Token berlaku sampai user logout atau dihapus manual

### Logout
- Panggil endpoint `POST /auth/logout` untuk revoke current token
- Hapus token dari local storage
- Navigasi kembali ke halaman login

### Error Handling
- `404 - User not found`: Redirect ke register page
- `409 - User already exists`: Redirect ke login page
- `401 - Unauthorized`: Token invalid/expired, hapus token dan redirect ke login

---

## 🎯 Status Appraisal

| Status         | Description                                         |
| -------------- | --------------------------------------------------- |
| `draft`        | Baru dibuat, masih bisa edit & hapus                |
| `submitted`    | Sudah disubmit, menunggu review admin               |
| `under_review` | Sedang direview oleh admin                          |
| `completed`    | Sudah selesai, ada `final_price` dan `admin_note`   |
| `rejected`     | Ditolak oleh admin, lihat `admin_note` untuk alasan |

---

## 💡 Tips untuk Frontend Developer

### Authentication with Firebase

#### Phone OTP — Login / Auto Register (Recommended)
```javascript
import { auth } from './firebase-config';
import { signInWithPhoneNumber } from 'firebase/auth';
import api from './api-service';

const loginOrRegisterWithPhone = async (phoneNumber, verificationCode) => {
  try {
    // 1. Kirim OTP ke nomor telepon via Firebase
    const confirmationResult = await signInWithPhoneNumber(auth, phoneNumber, appVerifier);

    // 2. User input kode OTP
    const result = await confirmationResult.confirm(verificationCode);

    // 3. Dapatkan ID Token (BUKAN UID!)
    const idToken = await result.user.getIdToken();

    // 4. Login atau register ke backend dengan satu endpoint
    const response = await api.post('/auth/phone', {
      id_token: idToken,
      phone_number: phoneNumber,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token, is_new_user } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));

      // is_new_user: true → isi profil dulu di profile_screen
      // is_new_user: false → langsung ke home_screen
      if (is_new_user) {
        navigation.navigate('Profile');
      } else {
        navigation.navigate('Home');
      }
    }
  } catch (error) {
    console.error('Phone auth failed:', error.response?.data?.message);
  }
};
```

#### Phone OTP — Register (Legacy)
```javascript
import { auth } from './firebase-config';
import { signInWithPhoneNumber } from 'firebase/auth';
import api from './api-service';

const registerWithPhone = async (phoneNumber, verificationCode, userData) => {
  try {
    // 1. Kirim OTP ke nomor telepon via Firebase
    const confirmationResult = await signInWithPhoneNumber(auth, phoneNumber, appVerifier);

    // 2. User input kode OTP
    const result = await confirmationResult.confirm(verificationCode);

    // 3. Dapatkan ID Token (BUKAN UID!)
    const idToken = await result.user.getIdToken();

    // 4. Register ke backend dengan ID Token
    const response = await api.post('/auth/register', {
      id_token: idToken,
      phone_number: phoneNumber,
      name: userData.name,
      email: userData.email,
      address: userData.address,
      gender: userData.gender,
      birth_date: userData.birthDate,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));
      navigation.navigate('Home');
    }
  } catch (error) {
    if (error.response?.status === 409) {
      Alert.alert('Error', 'User already registered, please login instead');
      navigation.navigate('Login');
    } else if (error.response?.status === 422) {
      console.error('Validation errors:', error.response.data.errors);
    } else {
      console.error('Registration failed:', error.response?.data?.message);
    }
  }
};
```

#### Phone OTP — Login (Legacy)
```javascript
const loginWithPhone = async (phoneNumber, verificationCode) => {
  try {
    const confirmationResult = await signInWithPhoneNumber(auth, phoneNumber, appVerifier);
    const result = await confirmationResult.confirm(verificationCode);
    const idToken = await result.user.getIdToken();

    const response = await api.post('/auth/login', {
      id_token: idToken,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));
      navigation.navigate('Home');
    }
  } catch (error) {
    if (error.response?.status === 404) {
      Alert.alert('Error', 'User not found, please register first');
      navigation.navigate('Register');
    } else {
      console.error('Login failed:', error.response?.data?.message);
    }
  }
};
```

#### Email & Password — Register
```javascript
import { createUserWithEmailAndPassword } from 'firebase/auth';

const registerWithEmail = async (email, password, userData) => {
  try {
    // 1. Buat akun di Firebase
    const result = await createUserWithEmailAndPassword(auth, email, password);

    // 2. Dapatkan ID Token
    const idToken = await result.user.getIdToken();

    // 3. Register ke backend (email diambil otomatis dari token)
    const response = await api.post('/auth/register/email', {
      id_token: idToken,
      name: userData.name,
      phone_number: userData.phoneNumber,
      address: userData.address,
      gender: userData.gender,
      birth_date: userData.birthDate,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));
      navigation.navigate('Home');
    }
  } catch (error) {
    if (error.response?.status === 409) {
      Alert.alert('Error', 'Email already registered, please login instead');
    } else {
      console.error('Registration failed:', error.response?.data?.message);
    }
  }
};
```

#### Email & Password — Login
```javascript
import { signInWithEmailAndPassword } from 'firebase/auth';

const loginWithEmail = async (email, password) => {
  try {
    // 1. Login di Firebase
    const result = await signInWithEmailAndPassword(auth, email, password);

    // 2. Dapatkan ID Token
    const idToken = await result.user.getIdToken();

    // 3. Login ke backend
    const response = await api.post('/auth/login/email', {
      id_token: idToken,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));
      navigation.navigate('Home');
    }
  } catch (error) {
    if (error.response?.status === 404) {
      Alert.alert('Error', 'User not found, please register first');
      navigation.navigate('RegisterEmail');
    } else {
      console.error('Login failed:', error.response?.data?.message);
    }
  }
};
```

#### Google Sign-In
```javascript
import { GoogleAuthProvider, signInWithCredential } from 'firebase/auth';
import { GoogleSignin } from '@react-native-google-signin/google-signin';

const loginWithGoogle = async () => {
  try {
    // 1. Google Sign-In → dapat Google ID Token
    await GoogleSignin.hasPlayServices();
    const { idToken: googleIdToken } = await GoogleSignin.signIn();

    // 2. Buat Firebase credential dari Google ID Token
    const googleCredential = GoogleAuthProvider.credential(googleIdToken);

    // 3. Sign in ke Firebase → dapat Firebase ID Token
    const result = await signInWithCredential(auth, googleCredential);
    const idToken = await result.user.getIdToken(); // ⚠️ Ini Firebase ID Token, BUKAN Google ID Token

    // 4. Login/register ke backend (satu endpoint)
    const response = await api.post('/auth/login/google', {
      id_token: idToken,
      fcm_token: await getFCMToken(),
    });

    if (response.data.success) {
      const { user, token, is_new_user } = response.data.data;
      await SecureStore.setItemAsync('auth_token', token);
      await SecureStore.setItemAsync('user_data', JSON.stringify(user));

      // Tampilkan onboarding jika user baru (belum punya phone/address)
      if (is_new_user) {
        navigation.navigate('CompleteProfile');
      } else {
        navigation.navigate('Home');
      }
    }
  } catch (error) {
    console.error('Google Sign-In failed:', error.response?.data?.message);
  }
};
```

#### Logout
```javascript
const logoutUser = async () => {
  try {
    // 1. Call backend logout to revoke token
    await api.post('/auth/logout');

    // 2. Sign out from Firebase
    await auth.signOut();

    // 3. Clear local storage
    await SecureStore.deleteItemAsync('auth_token');
    await SecureStore.deleteItemAsync('user_data');

    // 4. Navigate to login
    navigation.navigate('Login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};
```

#### Setup API Interceptor
```javascript
import axios from 'axios';
import * as SecureStore from 'expo-secure-store';

const api = axios.create({
  baseURL: 'http://your-api.com/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Request interceptor - attach token
api.interceptors.request.use(
  async (config) => {
    const token = await SecureStore.getItemAsync('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor - handle 401
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Token invalid/expired, clear and redirect to login
      await SecureStore.deleteItemAsync('auth_token');
      await SecureStore.deleteItemAsync('user_data');
      // Navigate to login page
    }
    return Promise.reject(error);
  }
);

export default api;
```

---

### Infinite Scroll Implementation
```javascript
const loadMore = async (cursor = null) => {
  const url = cursor
    ? `/appraisals?cursor=${cursor}`
    : '/appraisals';

  const response = await api.get(url);

  if (response.data.success) {
    const newItems = response.data.data;
    const nextCursor = response.data.meta.next_cursor;
    const hasMore = response.data.meta.has_more;

    // Append newItems to your list
    // Save nextCursor for next load
    // Update hasMore state
  }
};
```

### Image Upload
```javascript
const uploadPhoto = async (appraisalId, categoryName, imageFile) => {
  const formData = new FormData();
  formData.append('category_name', categoryName);
  formData.append('image', imageFile);

  const response = await api.post(
    `/appraisals/${appraisalId}/photos`,
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    }
  );

  return response.data;
};
```

### Display Image
Image path yang dikembalikan adalah relative path. Untuk menampilkan:
```javascript
const imageUrl = `${BASE_URL}/storage/${photo.image_path}`;
```

### Error Handling
```javascript
try {
  const response = await api.post('/appraisals', data);
  // Handle success
} catch (error) {
  if (error.response) {
    if (error.response.status === 422) {
      // Validation errors
      const errors = error.response.data.errors;
      // Display validation errors
    } else if (error.response.status === 401) {
      // Unauthorized - redirect to login
    } else if (error.response.status === 403) {
      // Forbidden - show error message
    } else if (error.response.status === 404) {
      // Not found
    }
  }
}
```

---

## 📝 Notes

- Semua timestamp menggunakan format ISO 8601 dengan timezone UTC
- File upload maksimal 5MB per foto
- Format gambar yang diterima: JPEG, PNG, JPG
- Cursor pagination lebih efisien untuk infinite scroll dibanding offset pagination
- Token Sanctum bersifat stateless, tidak perlu refresh token
- Untuk testing, bisa menggunakan Postman/Insomnia dengan collection yang sesuai

---

## 🚀 Getting Started

1. Set base URL API di konfigurasi aplikasi
2. Implementasi Firebase Authentication
3. Store & manage Sanctum token
4. Setup axios/fetch interceptor untuk attach token
5. Implement error handling globally
6. Test semua endpoint sesuai dokumentasi ini

Happy coding! 🎉
