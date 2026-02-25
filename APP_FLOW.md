# Yoshida Motors — Flutter Screen Instructions

---

## Screen 1 — `login_screen.dart`

**Purpose:** Entry point. User inputs phone number to receive OTP.

### UI Components
- Transparent AppBar or no AppBar
- Yoshida Motors logo centered at the top
- Title text: "Welcome Back", subtitle: "Enter your phone number to continue"
- TextField for phone number input with "+62" prefix, border color `primary500`, focus border `primary700`
- Full-width "Send OTP" button — background `primary600`, white text
- Background color `background` (white), light decorative accent using `primary50`

### Logic
- Validate phone number is not empty and has at least 9 digits
- On button tap → call Firebase Auth `verifyPhoneNumber()`
- Store the received `verificationId` from Firebase
- Navigate to `register_screen.dart` passing the `verificationId`

### Navigation
`login_screen` → `register_screen`

---

## Screen 2 — `register_screen.dart`

**Purpose:** OTP verification screen for the phone number entered on the login screen.

> Note: `register_screen` functions as the OTP Verification Screen, not a data registration form.

### UI Components
- Back button to `login_screen`
- Instruction text: "Enter the 6-digit code sent to [phone number]"
- 6 separate OTP input boxes — border color `neutral300`, active border `primary500`
- Countdown timer "Resend code in 0:59" — text color `neutral500`
- "Resend OTP" link — active after timer ends, color `accent500`
- "Verify" button — background `primary600`, white text; disabled (color `neutral300`) if OTP is not 6 digits

### Logic
- Auto-focus moves to the next box after each digit is entered
- When all 6 digits are filled → call `PhoneAuthProvider.credential()` then `signInWithCredential()`
- On success → check if user profile data exists in the database
  - Exists → navigate to `home_screen`
  - Does not exist → navigate to `profile_screen` for first-time setup
- On failure → show error SnackBar with color `secondary500`

### Navigation
`register_screen` → `home_screen` or `profile_screen`

---

## Screen 3 — `profile_screen.dart`

**Purpose:** Store user personal data (Name & Address). Used during onboarding and accessible from the menu.

### UI Components
- AppBar with title "My Profile", background `primary700`, white back icon
- Avatar/photo placeholder — circle shape, background `primary100`, person icon `primary500`
- TextField for "Full Name" — label "Full Name", person prefix icon
- TextField for "Address" — label "Address", maxLines 3, location prefix icon
- Full-width "Save Profile" button — background `primary600`, white text
- All field borders: color `neutral300`, focus border `primary500`

### Logic
- Pre-fill data if previously saved (from API / local storage)
- Validation: all fields are required
- On save → POST to `/api/profile` → show success SnackBar with color `primary500`
- If from onboarding (first time) → after save, navigate to `home_screen`

### Navigation
`profile_screen` → `home_screen` (if onboarding)

---

## Screen 4 — `home_screen.dart`

**Purpose:** Main dashboard. Central navigation hub for the user.

### UI Components
- Custom AppBar: logo on the left, user name and notification bell icon on the right, background `primary700`
  - Notification bell icon shows a red dot badge if there are unread notifications
- Greeting banner — "Hello, [Name]! Ready for your appraisal?", gradient background `primary600` → `primary900`
- Active Status Card (if an appraisal is in progress):
  - Status badge colors:
    - `Draft` → gray `neutral400`
    - `Submitted` → blue `primary500`
    - `Under Appraisal` → orange `accent500`
    - `Price Determined` → green `Color(0xFF22C55E)`
  - "View Details" button → navigate to `appraisal_result_screen`
- Main "Start New Appraisal" button — large, full-width, background `accent500`, white text, car icon
- Bottom Navigation Bar (optional): Home, History, Profile — active icon color `primary600`

### Logic
- On load → GET `/api/appraisals/latest` to check for an active appraisal
- Show status card if exists, hide if not
- Tap "Start New Appraisal" → navigate to `vehicle_info_screen`
- Tap Notification Bell → navigate to `notification_screen`

### Navigation
`home_screen` → `vehicle_info_screen` or `notification_screen`

---

## Screen 5 — `vehicle_info_screen.dart`

**Purpose:** User fills in detailed vehicle information for the appraisal.

### UI Components
- AppBar: title "Vehicle Information", background `primary700`
- Step Indicator at the top: Step 1 of 3 (Info → Photos → Summary), active color `primary600`, inactive `neutral300`
- Form fields (all with border `neutral300`, focus border `primary500`):
  - "Vehicle Brand" — Dropdown or TextField
  - "Vehicle Model" — TextField
  - "Year of Manufacture" — TextField (number type) or year dropdown
  - "License Plate" — TextField (uppercase)
  - "Mileage (km)" — TextField (number type)
  - "Additional Notes" — TextField, maxLines 4, optional
- "Next: Take Photos" button — full-width, background `primary600`, white text

### Logic
- Validate all required fields (except Notes)
- Save data to local state via Provider or Riverpod
- On next → Navigate to `photo_category_screen` passing the vehicle info data

### Navigation
`vehicle_info_screen` → `photo_category_screen`

---

## Screen 6 — `photo_category_screen.dart`

**Purpose:** Display the list of required photo categories with progress tracking.

### UI Components
- AppBar: title "Take Photos", background `primary700`
- Step Indicator: Step 2 of 3, active color `primary600`
- Overall progress bar at the top of the list — `LinearProgressIndicator`, color `primary500`
- Photo category list using `ListView`, each item as a `Card`:
  - Category icon (front view, side view, etc.)
  - Category name: "Front View", "Left Side", "Right Side", "Rear View", "Dashboard", "Engine Bay", "Interior"
  - Progress badge:
    - "0/1 Taken" → color `neutral400`
    - "1/1 Taken" → color `primary500` with checkmark icon
  - Tap → navigate to `camera_capture_screen` passing the category name
- "Continue to Summary" button:
  - Disabled (color `neutral300`) if any category has not been photographed
  - Active (color `primary600`) when all categories are complete

### Logic
- Store photo state per category in Provider/Riverpod or a local Map
- Count completed categories to determine button state
- On return from `camera_capture_screen`, refresh the status of the relevant category
- On "Continue to Summary" → POST to `/api/appraisals` to create a draft with vehicle info and all photos → store the returned `appraisalId`
- Navigate to `summary_screen` passing the `appraisalId`

### Navigation
`photo_category_screen` <-> `camera_capture_screen`, then → `summary_screen`

---

## Screen 7 — `camera_capture_screen.dart`

**Purpose:** Custom in-app camera to capture vehicle photos per category.

### UI Components
- Full-screen camera preview using the `camera` package
- Overlay guide frame — semi-transparent white rectangle as a framing guide
- Category label at the top: "Take Photo: Front View" — white text, semi-transparent `neutral900` background
- Shutter button (large circle, white) centered at the bottom
- Back button (bottom left) and flip camera button (bottom right) — white icons

### After Photo is Taken — Preview Mode
- Display the captured photo full screen
- Two buttons at the bottom:
  - "Retake" — white outlined button
  - "Use This Photo" — background `primary600`, white text
- On "Use This Photo" → save photo locally and return to `photo_category_screen` with updated category status

### Logic
- Use the `camera` package for camera access
- Compress photo before upload using `flutter_image_compress` package
- Handle camera errors (permission denied) → show dialog with "Open Settings" button

### Navigation
`camera_capture_screen` → back to `photo_category_screen`

---

## Screen 8 — `summary_screen.dart`

**Purpose:** Final review screen before submission — user checks all data one last time.

### UI Components
- AppBar: title "Review & Submit", background `primary700`
- Step Indicator: Step 3 of 3, active color `primary600`
- "Vehicle Information" section — Card with background `neutral50`, border `neutral200`:
  - Display all vehicle data from `vehicle_info_screen` in key-value format
- "Photos" section — 2-column grid of photo thumbnails with category label below each
  - Thumbnail border-radius 8, border `neutral200`
  - Tap photo → show full-screen preview
- Disclaimer text in small font, color `neutral500`: "By submitting, you agree that the information provided is accurate."
- "Submit Appraisal" button — full-width, background `accent500`, white text, send icon

### Logic
- Display all data collected from previous screens
- On Submit tap → PATCH `/api/appraisals/{id}` with status `"submitted"`
- Show loading dialog during the process
- On success → show success dialog: "Your appraisal has been submitted! We'll notify you once the review is complete." → navigate to `home_screen` (clear all previous routes)
- On failure → show error SnackBar with color `secondary500`

### Navigation
`summary_screen` → `home_screen` (after successful submission)

---

## Screen 9 — `appraisal_result_screen.dart`

**Purpose:** Display the final appraisal result determined by the admin.

### UI Components
- AppBar: title "Appraisal Result", background `primary700`
- Status Banner at the top:
  - If `under_appraisal` → orange/yellow banner `accent100`, text "Under Review — We'll notify you soon", clock icon `accent600`
  - If `price_determined` → green banner `Color(0xFFDCFCE7)`, text "Review Complete!", green checkmark icon
- Main Price Card (only visible if `price_determined`):
  - Label: "Offered Purchase Price", color `neutral500`
  - Large bold price number, color `primary700`, format: `Rp XX.XXX.XXX`
  - Card background `primary50`, border `primary200`
- Validity Period Card:
  - "Valid until: [date]" — calendar icon, text color `neutral700`
- "Next Steps" section — bullet list of actions for the user, bullet color `accent500`
- "Vehicle Details" section — summary of the appraised vehicle data
- "Contact Us" button (optional) — outlined border `primary500`, text color `primary600`

### Logic
- Fetch data from `/api/appraisals/{id}` on screen load
- If status is still `under_appraisal` → hide the price card
- Listen to FCM push notifications; if a notification arrives with the same `appraisalId` → automatically refresh the screen data
- Format price using the `intl` package: `NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ')`

### Navigation
Endpoint screen — no further navigation (other than back button to `home_screen`)

---

## Screen 10 — `notification_screen.dart`

**Purpose:** Display a list of notifications to the user.

### UI Components
- AppBar: title "Notifications", background `primary700`, back button to `home_screen`
- "Mark all as read" text button in the AppBar actions
- List of notifications using `ListView.builder`
  - Unread notifications have a slightly different background color (e.g., `primary50`) and a bold title
  - Read notifications have a white background (`background`) and normal text weight
  - Each item shows:
    - Icon based on notification type (e.g., info, success, warning)
    - Title text
    - Body text (max 2 lines, truncated)
    - Timestamp (e.g., "2 hours ago")
- Empty state view if there are no notifications: illustration and text "No notifications yet"

### Logic
- On load → GET `/api/notifications` to fetch the list of notifications (supports pagination)
- On tap "Mark all as read" → PUT `/api/notifications/mark-all-read` and refresh the list
- On tap a specific unread notification → PUT `/api/notifications/{id}/mark-read`
- If the notification contains an `appraisal_id` in its data payload, tapping it navigates to `appraisal_result_screen` for that specific appraisal

### Navigation
`notification_screen` → `appraisal_result_screen` (if applicable) or back to `home_screen`
