# FCM (Firebase Cloud Messaging) Documentation - Car Rongsok

Dokumentasi lengkap untuk integrasi Push Notification menggunakan Firebase Cloud Messaging (FCM) di sisi Client/Mobile App.

## 📌 Konsep Dasar

Aplikasi Car Rongsok menggunakan FCM untuk mengirimkan notifikasi real-time kepada User dan Admin.
- **User** akan menerima notifikasi ketika Admin membuat atau mengupdate status/harga dari Appraisal Request mereka.
- **Admin** akan menerima notifikasi ketika ada User yang men-submit Appraisal Request baru.

### ⚠️ Penting: FCM Token
Agar backend bisa mengirimkan notifikasi ke device yang tepat, **Frontend wajib mengirimkan `fcm_token`** ke backend.
Token ini disimpan di tabel `users` dan akan diupdate setiap kali user login atau token di-refresh oleh Firebase.

---

## 📱 Flow Implementasi di Mobile App (Flutter/Android/iOS)

### 1. Mendapatkan FCM Token
Saat aplikasi pertama kali dibuka atau setelah user berhasil login, aplikasi harus meminta permission untuk notifikasi dan mendapatkan FCM Token dari Firebase.

**Contoh di Flutter (menggunakan `firebase_messaging`):**
```dart
import 'package:firebase_messaging/firebase_messaging.dart';

FirebaseMessaging messaging = FirebaseMessaging.instance;

// 1. Minta permission (khusus iOS)
NotificationSettings settings = await messaging.requestPermission(
  alert: true,
  badge: true,
  sound: true,
);

// 2. Dapatkan FCM Token
String? fcmToken = await messaging.getToken();
print("FCM Token: $fcmToken");

// 3. Listen jika token berubah (Token Refresh)
FirebaseMessaging.instance.onTokenRefresh.listen((newToken) {
  // Kirim newToken ke backend (Update Profile API)
});
```

### 2. Mengirim FCM Token ke Backend
FCM Token harus dikirim ke backend agar backend tahu ke mana harus mengirim notifikasi.
Ada 2 cara untuk mengirim token ini:

**A. Saat Login / Register**
Sertakan `fcm_token` di body request saat memanggil endpoint Auth.
- `POST /api/v1/auth/phone`
- `POST /api/v1/auth/login/google`
- `POST /api/v1/auth/login/email`

```json
{
  "id_token": "eyJhbGciOiJSUzI1Ni...",
  "fcm_token": "fcm_device_token_here"
}
```

**B. Saat Token Berubah (Token Refresh) atau Update Profile**
Jika token berubah saat user sedang login, kirim token baru melalui endpoint Update Profile.
- `PUT /api/v1/auth/profile`

```json
{
  "fcm_token": "new_fcm_device_token_here"
}
```

---

## �️ Database Notifikasi (In-App Notifications)

Selain mengirimkan Push Notification via FCM, backend juga menyimpan riwayat notifikasi di database. Ini berguna untuk menampilkan halaman "Notifikasi" di dalam aplikasi.

### 1. Get All Notifications
**Endpoint:** `GET /api/v1/notifications`
**Auth Required:** Yes

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notifications retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "title": "Appraisal Update",
      "body": "Your appraisal for Honda Jazz has been updated to completed.",
      "data": {
        "type": "appraisal_updated",
        "appraisal_id": "12",
        "status": "completed"
      },
      "is_read": false,
      "created_at": "2026-02-23T04:30:00.000000Z",
      "updated_at": "2026-02-23T04:30:00.000000Z"
    }
  ],
  "meta": {
    "per_page": 15,
    "next_cursor": "eyJpZCI6MTUsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0=",
    "prev_cursor": null,
    "has_more": true
  }
}
```

### 2. Mark Notification as Read
**Endpoint:** `PUT /api/v1/notifications/{id}/mark-read`
**Auth Required:** Yes

**Success Response (200):**
```json
{
  "success": true,
  "message": "Notification marked as read",
  "data": {
    "id": 1,
    "is_read": true,
    ...
  }
}
```

### 3. Mark All Notifications as Read
**Endpoint:** `PUT /api/v1/notifications/mark-all-read`
**Auth Required:** Yes

**Success Response (200):**
```json
{
  "success": true,
  "message": "All notifications marked as read",
  "data": null
}
```

---

## �🔔 Jenis Notifikasi (Payload Data)

Backend akan mengirimkan notifikasi dengan format standar FCM yang berisi `notification` (untuk ditampilkan di system tray) dan `data` (untuk di-handle oleh aplikasi saat di-tap).

### 1. Notifikasi untuk User: Appraisal Dibuat oleh Admin
Dikirim ketika Admin membuatkan Appraisal Request secara manual untuk User.

**Payload yang diterima Mobile App:**
```json
{
  "notification": {
    "title": "New Appraisal Request",
    "body": "An appraisal request for your Honda Jazz has been created by Admin."
  },
  "data": {
    "type": "appraisal_created",
    "appraisal_id": "12"
  }
}
```

### 2. Notifikasi untuk User: Appraisal Diupdate oleh Admin
Dikirim ketika Admin mengubah status (misal: `under_review`, `completed`) atau memberikan harga final (`final_price`).

**Payload yang diterima Mobile App:**
```json
{
  "notification": {
    "title": "Appraisal Update",
    "body": "Your appraisal for Honda Jazz has been updated to completed. Final price: Rp 150.000.000."
  },
  "data": {
    "type": "appraisal_updated",
    "appraisal_id": "12",
    "status": "completed"
  }
}
```

### 3. Notifikasi untuk Admin: User Submit Appraisal Baru
Dikirim ke semua Admin ketika ada User yang men-submit Appraisal Request dari status `draft` menjadi `submitted`.

**Payload yang diterima Mobile App (Admin):**
```json
{
  "notification": {
    "title": "New Appraisal Submitted",
    "body": "A new appraisal request for Toyota Avanza has been submitted by Budi."
  },
  "data": {
    "type": "appraisal_submitted",
    "appraisal_id": "15"
  }
}
```

---

## 🛠️ Handling Notifikasi di Mobile App

Aplikasi harus bisa menangani notifikasi dalam 3 state:
1. **Foreground**: Aplikasi sedang dibuka. (Biasanya notifikasi tidak muncul di system tray, harus ditangani manual dengan menampilkan in-app alert/snackbar).
2. **Background**: Aplikasi berjalan di background. (Notifikasi muncul di system tray, jika di-tap akan membuka aplikasi).
3. **Terminated**: Aplikasi ditutup sepenuhnya. (Notifikasi muncul di system tray, jika di-tap akan membuka aplikasi dari awal).

**Contoh Handling di Flutter:**
```dart
// 1. Foreground
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Got a message whilst in the foreground!');
  print('Message data: ${message.data}');

  if (message.notification != null) {
    // Tampilkan Snackbar atau In-App Alert
    showInAppNotification(message.notification!.title, message.notification!.body);
  }
});

// 2. Background / Terminated (Saat notifikasi di-tap)
FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  print('A new onMessageOpenedApp event was published!');

  // Navigasi berdasarkan tipe notifikasi
  if (message.data['type'] == 'appraisal_updated' || message.data['type'] == 'appraisal_created') {
    String appraisalId = message.data['appraisal_id'];
    // Arahkan user ke halaman detail appraisal
    navigatorKey.currentState?.pushNamed('/appraisal-detail', arguments: appraisalId);
  }
});
```

## 🧪 Testing Notifikasi
Untuk melakukan testing apakah FCM sudah berjalan dengan baik:
1. Pastikan device/emulator sudah mendapatkan FCM Token.
2. Login ke aplikasi agar FCM Token tersimpan di database backend.
3. Lakukan trigger action (misal: submit appraisal dari user, atau update status dari admin panel).
4. Cek apakah notifikasi masuk ke device.
