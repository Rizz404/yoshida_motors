# Yoshida Motors — End-User App Flow

This document describes the complete user journey from the end-user (customer) perspective when using the Yoshida Motors app. It is based on all screens and logic within the application.

---

## 1. Authentication (Login & Register)

Unauthenticated users are automatically redirected to the Login screen by `AuthGuard`.

### 1a. Login (`LoginScreen`)

- The login screen displays **two tabs**: **Email** and **Phone/OTP**.
- **Email tab (active):**
  - Form with **Email** and **Password** fields (with validation).
  - **"Login"** button to sign in via Firebase Authentication with email & password.
  - **"Continue with Google"** button for Google OAuth sign-in.
  - On successful Firebase auth, the ID Token is sent to the backend along with the FCM Token to establish a session.
- **Phone/OTP tab (disabled):**
  - Currently unavailable. Displays a message that phone login is not yet supported.
- A **"Don't have an account? Register"** link at the bottom navigates to the Register screen.
- On successful login, a success toast is shown and the user is automatically navigated to **AppShellScreen** (Home) via `reevaluateListenable` + `AuthGuard`.

### 1b. Register (`RegisterScreen`)

- Also displays **two tabs**: **Email** and **Phone/OTP**.
- **Email tab (active):**
  - Form fields: **Email** (required), **Password** (required, validated), **Confirm Password** (required), **Full Name** (optional), **Address** (optional).
  - **"Register"** button to create a new account via Firebase, then register with the backend.
  - **"Continue with Google"** button for auto register+login via Google.
  - If the account already exists (`already registered`), an error toast is shown and the user is redirected to the Login screen after 2 seconds.
- **Phone/OTP tab (disabled):**
  - Same as Login — displays a message that phone registration is not yet available.
- An **"Already have an account? Login"** link at the bottom navigates to the Login screen.

---

## 2. Main Navigation (`AppShellScreen`)

After successful login, the user enters the `AppShellScreen` which contains:

- **AppBar** at the top:
  - Home tab shows the app icon + "Yoshida Motors" text.
  - Profile tab shows "My Profile" text.
  - On the right side: the user's profile photo (or initials if no photo is set).
- **Bottom Navigation Bar** with 2 tabs:
  - **Home** (index 0, default): The main dashboard.
  - **Profile** (index 1): The user profile page.
- **Drawer** (left side menu) containing:
  - Header with app logo and name.
  - User info section (profile photo, name, email/phone) — tap to navigate to Profile.
  - Navigation menu: **Home**, **Appraisal** (appraisal list), **Notification** (notification list).
  - **Settings:**
    - **Theme Switcher**: Toggle between dark/light mode.
    - **Language Switcher**: Dropdown to select language (English 🇬🇧 / Japanese 🇯🇵).
  - **Logout** button: Shows a "Logging out..." toast, closes the drawer, then performs logout. Navigation back to Login is handled automatically by `AuthGuard`.

---

## 3. Home Dashboard (`HomeScreen`)

The main screen displaying:

1. **Greeting Banner**:
   - Gradient card with a personalized greeting (e.g., "Hello, [name]!") and a prompt to start an appraisal.

2. **Latest Appraisal Card**:
   - Section header "Latest Appraisal" with **Refresh** and **See All** buttons.
   - **Loading state** (no data yet): Shows a loading spinner.
   - **Has latest appraisal**: Displays a card with:
     - Vehicle name (Brand + Model) and year.
     - Colored status badge: **Draft** (gray), **Submitted** (primary), **Under Review** (accent), **Completed** (green), **Rejected** (red).
     - Offered price if available (formatted in Yen ¥).
     - **"View Details"** button → navigates to `AppraisalResultScreen`.
   - **No appraisals**: Shows an icon and "No appraisal found" message.
   - Pull-to-refresh is available.

3. **"Start New Appraisal" Button**:
   - Before navigating, a **profile completeness check** is performed: name, address, gender, phone number, and birth date must all be filled.
   - If profile is incomplete: an error toast is shown asking the user to complete their profile first.
   - If profile is complete: navigates to `VehicleInfoScreen` (Step 1).

---

## 4. New Appraisal Flow

The flow consists of 3 sequential steps, each indicated by a **Step Indicator** at the top.

### 4a. Step 1 — Vehicle Information (`VehicleInfoScreen`)

- **Step Indicator**: Step 1 of 3.
- AppBar: "Vehicle Information".
- Form fields:
  - **Vehicle Brand** (required, validated)
  - **Vehicle Model** (required, validated)
  - **Year of Manufacture** (required, numeric, validated)
  - **License Plate** (optional)
  - **Mileage** (optional, numeric)
  - **Notes / Description** (optional, multiline, length validated)
- **"Next"** button: Saves data to `appraisalFormProvider` (local state) and navigates to `PhotoCategoryScreen`.
- No data is sent to the server at this step (local state only).

### 4b. Step 2 — Vehicle Photos (`PhotoCategoryScreen`)

- **Step Indicator**: Step 2 of 3.
- AppBar: "Vehicle Photos".
- Displays a **warning banner** about photo quality requirements.
- **"Add New Photo"** section (collapsible) with two buttons:
  - **Camera**: Opens `CameraCaptureScreen` to take a photo using the device camera.
  - **Upload**: Opens the gallery for multi-select image picking.
- **Maximum 7 photos**. If already at 7, the add buttons are hidden and an error toast appears if the user tries to add more.
- **Uploaded photos list**: Each photo card (`AppraisalPhotoCard`) shows:
  - Image preview.
  - Editable category/label text field.
  - Delete button.
- If no photos yet: "No photos uploaded yet" message.
- **"Continue"** button: Only enabled when **at least 1 photo** has been added.
  - Validation: all photos must have a non-empty category label.
  - On submit, **the appraisal (vehicle info + photos) is created on the server** via `createAppraisal()`.
  - On success: `currentAppraisalIdProvider` is set with the newly created appraisal ID, then navigates to `SummaryScreen`.

### 4b-i. In-App Camera (`CameraCaptureScreen`)

- The device camera **opens automatically** upon entering this screen.
- If the user cancels photo capture, they are returned to the previous screen.
- After a photo is taken, a **preview** is shown with two options:
  - **Retake**: Re-opens the camera to take another photo.
  - **Use This Photo**: Adds the photo to the appraisal form state with an empty label (editable later in `PhotoCategoryScreen`).
- A success toast "Photo added!" is shown, then returns to the previous screen.

### 4c. Step 3 — Review & Submit (`SummaryScreen`)

- **Step Indicator**: Step 3 of 3.
- AppBar: "Review & Submit".
- Displays the appraisal data **already created on the server** (not from local state), fetched via `appraisalDetailNotifierProvider`.
- **Vehicle Information Section**: Shows Brand, Model, Year, License Plate, Mileage, and Notes in a table layout.
- **Photos Section**: 2-column grid of photo thumbnails with category names. Photos can be tapped for full preview.
- **Disclaimer text** at the bottom.
- **Two action buttons**:
  - **"Back to Home" (Save as Draft)**: Shows a success toast "Draft saved!" and navigates back to Home (replaces entire stack). The appraisal status remains **Draft**.
  - **"Submit Appraisal"**: Sends a request to the backend to change status to **Submitted**. On success, shows a success toast and navigates back to Home.

---

## 5. Appraisal History (`ListAppraisalsScreen`)

- Accessed via: **"See All"** button on Home, or **"Appraisal"** menu in the Drawer.
- AppBar: "My Appraisals" with a Refresh button.
- Pull-to-refresh is available.
- **Appraisal list** displayed as cards with **pagination** (infinite scroll — auto-loads when scrolling to the bottom):
  - Each card shows: Brand + Model, Year • License Plate, colored status badge.
  - If `finalPrice` exists: displayed in Yen format.
  - **"View Details"** button → navigates to `AppraisalResultScreen`.
- **No appraisals**: Shows an icon and "No appraisals yet" message.

---

## 6. Appraisal Detail & Result (`AppraisalResultScreen`)

Displays the full detail of an appraisal, with different UI depending on the status:

### Status Banner
Always shown at the top, with different content and color per status:
- **Under Review** (accent): Clock icon + message that it's being reviewed by the team.
- **Completed** (green): Checkmark icon + message that the appraisal is complete.
- **Rejected** (red): Cancel icon + message that the appraisal was rejected.

### Status-Specific Content

- **Completed (Price Determined)**:
  - **Price Card**: Displays the **Offered Purchase Price** in Yen (¥) with a large headline.
  - Shows **"Valid until [date]"** (30 days from the last update date).
  - **Next Steps** guide: 4 steps for the handover and payment process.

- **Under Review**:
  - **Next Steps** guide: 3 steps explaining what to expect while waiting.
  - **"Contact Us"** button for reaching out to Yoshida Motors.

- **Rejected**:
  - **Rejection Reason**: A red banner showing the admin's rejection note/reason.

- **Non-rejected with Admin Note**:
  - **Admin Notes**: A blue/info banner showing admin comments.

- **Draft**:
  - **"Edit Appraisal"** button → navigates to `EditAppraisalScreen`.

### Common Content (all statuses)
- **Vehicle Details**: Table with Brand, Model, Year, License Plate, Mileage, and Notes.
- **Photos**: 2-column grid of vehicle photos with category names. Photos can be tapped for full preview.

---

## 7. Edit Draft Appraisal (`EditAppraisalScreen`)

- Only accessible for appraisals with **Draft** status (via the "Edit Appraisal" button in `AppraisalResultScreen`).
- AppBar: "Edit Appraisal".
- **Vehicle Information Section**: Same form as Step 1 (Brand, Model, Year, License Plate, Mileage, Notes) — pre-filled with existing data.
- **Photos Section** (`Photos (N/7)`):
  - Shows existing server photos (each with a delete button).
  - Shows newly added local photos (each with a category label field and delete button).
  - **"Add Photo"** button (only visible if total photos < 7) opens the gallery for multi-select.
  - Maximum 7 photos total (existing + new).
- **"Save Changes"** button:
  - Validation: form must be valid, at least 1 photo total, all new photos must have a category label.
  - Uses **PATCH** (only sends changed data) via `UpdateAppraisalPayload.fromChanges()`.
  - If nothing changed: success toast without an API call.
  - On success: data is refreshed, success toast shown, then navigates back.

---

## 8. Notifications (`ListNotificationsScreen`)

- Accessed via the **"Notification"** menu in the Drawer.
- AppBar: "Notifications".
  - If there are unread notifications (`unreadCount > 0`): a **"Mark all as read"** button appears in the AppBar.
- Pull-to-refresh is available.
- **Notification list** with **pagination** (infinite scroll):
  - Each card shows: **title**, **body**, and **time** (time-ago format).
  - Unread notifications have: a tinted background, bold title, and colored border.
  - Tapping an unread notification automatically marks it as **read**.
- **No notifications**: Shows a bell icon and "No notifications yet" message.

---

## 9. User Profile (`ProfileScreen`)

- Accessed via the **Profile** tab in the Bottom Navigation or via the Drawer (tap user info).
- AppBar: "My Profile".
- **Avatar**: Displays the profile photo (from server) or a default icon if none exists.
- Below the avatar: the user's email or phone number.
- **Profile Form** (all fields pre-filled with existing data):
  - **Profile Photo**: File picker for uploading a new photo (max 2 MB, formats: jpg/jpeg/png/webp).
  - **Full Name** (text, validated).
  - **Email** (disabled/read-only if auth provider is Google or Email).
  - **Address** (multiline).
  - **Phone Number** (disabled/read-only if auth provider is Phone).
  - **Gender** (dropdown: Male, Female, Other).
  - **Birth Date** (date picker, range 1900 – today).
- **"Save"** button:
  - Uses **PATCH** (only sends changed data) via `UpdateUserPayload.fromChanges()`.
  - If nothing changed: success toast without an API call.
  - On success: success toast, keyboard dismissed, file picker reset.
  - On failure: error toast.

---

## 10. Appraisal Status Reference

| Status         | Label        | Description                                          | Color   |
| -------------- | ------------ | ---------------------------------------------------- | ------- |
| `draft`        | Draft        | Saved locally, not yet submitted to admin            | Gray    |
| `submitted`    | Submitted    | Sent to admin, waiting for review                    | Primary |
| `under_review` | Under Review | Being reviewed by the Yoshida Motors team            | Accent  |
| `completed`    | Completed    | Appraisal complete, offered purchase price available | Green   |
| `rejected`     | Rejected     | Rejected by admin with a reason                      | Red     |

---

## 11. Route Map

```
/auth/login          → LoginScreen (initial)
/auth/register       → RegisterScreen

/ (AppShellScreen, guarded)
├── /home            → HomeScreen (tab 0, default)
└── /profile         → ProfileScreen (tab 1)

/appraisal/vehicle-info     → VehicleInfoScreen (Step 1)
/appraisal/photo-category   → PhotoCategoryScreen (Step 2)
/appraisal/camera-capture   → CameraCaptureScreen
/appraisal/summary          → SummaryScreen (Step 3)
/appraisal/list             → ListAppraisalsScreen
/appraisal/result           → AppraisalResultScreen
/appraisal/edit/:id         → EditAppraisalScreen

/notification/list          → ListNotificationsScreen
```

All routes except `/auth/login` and `/auth/register` are protected by `AuthGuard`. Unauthenticated users are automatically redirected to `LoginScreen`.
