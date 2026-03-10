# Yoshida Motors — Admin Web Flow

This document describes the complete administrator journey from the admin's perspective when using the Yoshida Motors Admin Panel. It covers all screens, logic, and actions available within the web-based admin interface.

---

## 1. Authentication (Login)

Unauthenticated administrators are automatically redirected to the Login page by the `auth` middleware.

### Login (`/login`)

- The login page displays a centered card with the **Yoshida Motors** branding.
- **Language Switcher**: Top-right corner of the card allows switching between **EN** (English) and **JA** (Japanese). The selected locale is reflected in the URL prefix (e.g., `/en/login` or `/ja/login`).
- **Form fields**:
  - **Email Address** (required)
  - **Password** (required)
  - **Remember Me** checkbox
  - **Forgot Password** link (UI only, no functionality currently wired)
- Submitting the form calls `POST /login`, processed by `LoginController`.
- On successful login: the admin is redirected to the **Dashboard**.
- On failure: validation error messages are shown inline next to each field.
- **Guest-only route**: Authenticated admins who visit `/login` are automatically redirected away.

---

## 2. Admin Shell / Layout

After login, the admin operates within a persistent shell layout that wraps all admin pages.

### Sidebar Navigation

A fixed left sidebar with a dark teal background (`#0f3d3a`) contains:

- **Logo / Brand** at the top: "Yoshida**Motors**" in styled text, links back to the Dashboard.
- **Navigation Links** (with active-state highlighting):
  | Label           | Route            |
  | --------------- | ---------------- |
  | Dashboard       | `/dashboard`     |
  | Appraisals      | `/appraisals`    |
  | Notifications   | `/notifications` |
  | User Management | `/users`         |
- **Logout Button** at the bottom: submits a `POST /logout` form, clears the session, and redirects to the Login page.

### Top Bar

Each page has a page-level heading and contextual action buttons rendered inline at the top of the content area (e.g., "Add New User" or "New Request" buttons).

### Flash Notifications

After any create, update, or delete action, a color-coded notification banner is displayed at the top of the page:
- **Success**: Green border-left card with a title and message.
- **Error**: Red border-left card with a title and message. In the local development environment, a full stack trace is also shown.

---

## 3. Dashboard (`/dashboard`)

The main overview page for the admin. Powered by `DashboardController`.

### Statistics Cards

Four summary cards are displayed in a responsive grid:

| Card                      | Description                                                                            |
| ------------------------- | -------------------------------------------------------------------------------------- |
| **Total Vehicles**        | Total count of all appraisal requests ever submitted (all statuses).                   |
| **Pending Reviews**       | Count of appraisals with status `submitted` — waiting for admin action.                |
| **Under Review**          | Count of appraisals with status `under_review` — currently being processed.            |
| **Total Appraised Value** | Sum of `final_price` for all appraisals with status `completed`, displayed in Yen (¥). |

### Recent Submissions Table

Below the stat cards, a table displays the **5 most recently created** appraisal requests with the following columns:

- **Owner**: Profile avatar (or initials fallback) + full name + email.
- **Car Details**: Vehicle brand + model + manufacture year.
- **Date**: Submission date formatted as `Mon DD, YYYY`.
- **Status**: Color-coded status badge (see [Status Reference](#7-appraisal-status-reference)).
- **Action**: A **"Review"** link that navigates directly to the appraisal's edit page.

A **"View All"** link at the top-right of the table navigates to the full Appraisals list.

---

## 4. Appraisal Management

Managed by `Admin\AppraisalRequestController` using standard Laravel resource routing under `/appraisals`.

> **Note:** Appraisals with status `draft` are **hidden** from the admin list. Drafts are still being prepared by the customer and have not been officially submitted.

---

### 4a. Appraisal List (`GET /appraisals`)

- **Page heading**: "Appraisal Requests" with a **"+ New Request"** button on the right.
- Appraisals are listed in a paginated table (**10 per page**), ordered by newest first.
- **Table columns**:
  - **Photo**: Thumbnail of the first photo attached to the appraisal (with full-size preview on click).
  - **Vehicle Info**: Year, Brand, Model, License Plate (if available), Mileage (if available).
  - **Owner**: Customer's full name and email.
  - **Status**: Color-coded badge.
  - **Est. Price**: `final_price` formatted as `¥X,XXX,XXX`, or "Pending" if not yet set.
  - **Actions**:
    - **Review** → navigates to the Edit/Review page.
    - **Delete** → triggers a browser confirmation dialog before submitting `DELETE /appraisals/{id}`.

---

### 4b. Create Appraisal (`GET /appraisals/create` → `POST /appraisals`)

Used when an admin needs to manually create an appraisal on behalf of a customer.

- **Page heading**: "New Appraisal Request" with a back link to the list.
- The form is divided into two sections:

**Section 1 — Vehicle Information:**
- **Customer / Owner** (required): Dropdown list of all registered users (name + email).
- **Vehicle Brand** (required): Text input.
- **Vehicle Model** (required): Text input.
- **Year of Manufacture** (required): Number input, range 1900 – next year.
- **License Plate** (optional): Text input.
- **Mileage** (optional): Number input (km).
- **Description / Notes** (optional): Textarea.
- **Initial Status** (required): Dropdown — Draft, Submitted, Under Review, Completed, Rejected.
- **Final Price** (optional): Numeric input (Yen).

**Section 2 — Vehicle Photos:**
- **Multiple photo upload** (`new_photos[]`): Accepts JPEG/PNG, max 2 MB each, maximum 7 photos.
- Each uploaded file shows a preview thumbnail.
- Optional **photo label** (`photo_labels[]`) can be assigned per photo; defaults to `"General"` if left blank.

On successful save:
- The appraisal record and all photos are created within a **database transaction**.
- A **push notification** (FCM) is sent to the customer's device if they have an FCM token registered.
- An in-app notification record is also created in the `notifications` table.
- Admin is redirected to the Appraisals list with a success banner.

On failure: the form is re-displayed with validation errors and previously entered values.

---

### 4c. Review / Edit Appraisal (`GET /appraisals/{id}/edit` → `PUT /appraisals/{id}`)

The primary screen for reviewing and processing a customer's appraisal submission.

- **Page heading**: "Review Appraisal #`{id}`" + vehicle brand & model subtitle. A back link to the list is shown on the right.
- The form is split into a **two-column layout**:

**Left Column (2/3 width):**

- **Vehicle Details Card** *(Read-Only)*:
  - Displays Brand, Model, Year, License Plate, Mileage, and Description in a structured grid.
  - Marked with a 🔒 "Read-only" badge — admins cannot edit customer-submitted vehicle data.

- **Photos Management Card**:
  - **Existing Photos**: Displayed in a 2–3 column grid. Each photo shows:
    - A full-size preview on click.
    - The category label below the image.
    - A **"Delete Photo"** checkbox overlay — checking it marks that photo for deletion on save.
  - **Add New Photos**: File input for uploading additional photos (JPEG/PNG, max 2 MB each). Previews are shown immediately using JavaScript (`Alpine.js`).
  - **New Photo Labels**: A text field per newly uploaded photo to assign a category label; defaults to `"Additional Photo"`.

**Right Column (1/3 width):**

- **Admin Actions Card** — the only editable fields for an existing appraisal:
  - **Status** (required): Dropdown — Submitted, Under Review, Completed, Rejected.
  - **Offered Purchase Price** (optional): Numeric input in Yen (¥). Typically filled when setting status to `completed`.
  - **Price Valid Until** (optional): Date picker for the offer's expiry date.
  - **Admin Note / Rejection Reason** (optional): Textarea. When status is set to `rejected`, this note is shown to the customer as the rejection reason.

- **Save Changes** button: Submits the form.

On successful save:
- Status, price, `price_valid_until`, and `admin_note` are updated.
- Selected photos are permanently deleted (both DB record and file on disk).
- New photos are uploaded and stored.
- All operations run within a **database transaction**.
- A **push notification** is sent to the customer:
  - If status is `rejected`: a rejection notification with the admin note (if provided).
  - Otherwise: a status update notification, including the offered price if set.
- An in-app notification record is saved.
- Admin is redirected to the Appraisals list with a success banner.

---

### 4d. Delete Appraisal (`DELETE /appraisals/{id}`)

- Triggered from the **Delete** button in the Appraisals list (or the edit page).
- A browser confirmation dialog is shown first: *"Are you sure you want to delete this appraisal?"*
- On confirmation:
  - All associated photo **files are deleted from disk** first.
  - The appraisal record is deleted (cascades to `appraisal_photos` table via DB constraint).
  - All operations run within a **database transaction**.
- Admin is redirected to the Appraisals list with a success banner.

---

## 5. User Management

Managed by `Admin\UserController` using standard Laravel resource routing under `/users`.

---

### 5a. User List (`GET /users`)

- **Page heading**: "User Management" with a **"+ Add New User"** button on the right.
- Users are listed in a paginated table (**10 per page**), ordered by newest first.
- **Table columns**:
  - **User**: Profile avatar (initials fallback) + full name + user ID.
  - **Contact**: Email (with ✓ verified / ! unverified badge) + phone number + address (truncated).
  - **Details**: Gender + birth date.
  - **Auth Provider**: Badge showing how the user registered — `email`, `phone`, or `google`.
  - **Role**: Badge showing `user` or `admin`.
  - **Joined Date**: Account creation date.
  - **Actions**:
    - **Edit** → navigates to the Edit User page.
    - **Delete** → triggers a browser confirmation dialog before submitting `DELETE /users/{id}`.

---

### 5b. Create User (`GET /users/create` → `POST /users`)

- **Page heading**: "Add New User" with a back link to the list.
- **Form fields**:
  - **Full Name** (required)
  - **Email Address** (required, must be unique)
  - **Phone Number** (optional, must be unique)
  - **Role** (required): Dropdown — `User` or `Admin`.
  - **Password** (required) + **Confirm Password** (required)
  - **Address** (optional): Textarea.
  - **Profile Photo** (optional): Image file (JPEG/PNG/GIF, max 2 MB). Stored in `storage/app/public/profile_photos/`.

On successful save: admin is redirected to the Users list with a success banner.

---

### 5c. Edit User (`GET /users/{id}/edit` → `PUT /users/{id}`)

- **Page heading**: "Edit User — `{name}`" with a back link to the list.
- **Form fields** (all pre-filled with existing data):
  - **Full Name** (required)
  - **Email Address**: Locked (read-only) if the user registered via `email` or `google` provider. Editable otherwise.
  - **Phone Number**: Locked (read-only) if the user registered via `phone` provider. Editable otherwise.
  - **Role**: Dropdown — `User` or `Admin`.
  - **Gender**: Dropdown — Male, Female, Other (optional).
  - **Birth Date**: Date picker (optional).
  - **Change Password** section (optional): New Password + Confirm New Password. If left blank, the existing password is preserved.
  - **Address** (optional): Textarea.
  - **Profile Photo** (optional): Shows the current photo with a preview. Upload a new file to replace it. The old file is deleted from disk upon replacement.

On successful save: admin is redirected to the Users list with a success banner.

---

### 5d. Delete User (`DELETE /users/{id}`)

- Triggered from the **Delete** button in the Users list.
- A browser confirmation dialog is shown first.
- On confirmation:
  - The user's **profile photo file is deleted from disk** (if it exists).
  - The user record is deleted from the database.
- Admin is redirected to the Users list with a success banner.

---

## 6. Notifications (`/notifications`)

Managed by `Admin\NotificationController`. These are system notifications directed to the **admin account** (e.g., triggered by appraisal events).

---

### 6a. Notification List (`GET /notifications`)

- **Page heading**: "Notifications" with a **"✓ Mark All as Read"** button on the right.
- Notifications are listed in a paginated table (**10 per page**), ordered by newest first.
- Unread notifications are visually highlighted with a light primary-colored row background.
- **Table columns**:
  - **Status**: Badge — `New` (blue) for unread, `Read` (grey) for read.
  - **Title & Body**: Bold title + truncated body preview. Unread titles appear in full primary text color.
  - **Date**: Relative timestamp (e.g., "2 hours ago").
  - **Actions**:
    - **View** (eye icon) → navigates to the notification detail page (and auto-marks as read).
    - **Mark as Read** (checkmark icon) → only visible for unread notifications. Marks a single notification as read without navigating away.
    - **Delete** (trash icon) → triggers a browser confirmation dialog before deleting.

**"Mark All as Read"** button: sends `POST /notifications/mark-all-read`, updates all unread notifications for the current admin to `is_read = true`. Admin is redirected back to the list with a success banner.

---

### 6b. Notification Detail (`GET /notifications/{id}`)

- **Page heading**: "Notification Detail" with a back button to the list.
- Automatically marks the notification as **read** when the page is opened.
- **Content displayed**:
  - **Title** (large, bold) + **Timestamp** (formatted as `DD Mon YYYY, HH:MM`).
  - **Body**: Full notification message text.
  - **Additional Data** (if present): Displays the raw `data` JSON payload from the notification in a monospace preformatted block (e.g., `type`, `appraisal_id`, `status`).
- **Action Buttons** at the bottom:
  - **"View Appraisal"** button: Visible only if the notification's `data` includes an `appraisal_id`. Navigates directly to the appraisal's edit/review page (`/appraisals/{id}/edit`).
  - **Delete** button: Triggers a browser confirmation dialog, then deletes the notification and redirects to the list.

---

## 7. Appraisal Status Reference

| Status         | Color          | Description                                                                             |
| -------------- | -------------- | --------------------------------------------------------------------------------------- |
| `draft`        | Grey           | Created by the customer but not yet submitted. Hidden from the admin list.              |
| `submitted`    | Blue           | Customer has submitted the appraisal and is awaiting admin review.                      |
| `under_review` | Yellow/Warning | Admin has started reviewing the appraisal.                                              |
| `completed`    | Green          | Review is complete. A final purchase price has been set and offered to the customer.    |
| `rejected`     | Red            | The appraisal was rejected. A rejection reason (admin note) is visible to the customer. |

---

## 8. Route Summary

All admin routes are prefixed with `/{locale}` where `locale` is `en` or `ja`.

| Method   | URI                                      | Controller Action                      | Description           |
| -------- | ---------------------------------------- | -------------------------------------- | --------------------- |
| `GET`    | `/{locale}/login`                        | `LoginController@showLoginForm`        | Show login form       |
| `POST`   | `/{locale}/login`                        | `LoginController@login`                | Process login         |
| `POST`   | `/{locale}/logout`                       | `LoginController@logout`               | Logout                |
| `GET`    | `/{locale}/dashboard`                    | `DashboardController`                  | Admin dashboard       |
| `GET`    | `/{locale}/appraisals`                   | `AppraisalRequestController@index`     | List appraisals       |
| `GET`    | `/{locale}/appraisals/create`            | `AppraisalRequestController@create`    | Create appraisal form |
| `POST`   | `/{locale}/appraisals`                   | `AppraisalRequestController@store`     | Store new appraisal   |
| `GET`    | `/{locale}/appraisals/{id}/edit`         | `AppraisalRequestController@edit`      | Edit/review appraisal |
| `PUT`    | `/{locale}/appraisals/{id}`              | `AppraisalRequestController@update`    | Update appraisal      |
| `DELETE` | `/{locale}/appraisals/{id}`              | `AppraisalRequestController@destroy`   | Delete appraisal      |
| `GET`    | `/{locale}/users`                        | `UserController@index`                 | List users            |
| `GET`    | `/{locale}/users/create`                 | `UserController@create`                | Create user form      |
| `POST`   | `/{locale}/users`                        | `UserController@store`                 | Store new user        |
| `GET`    | `/{locale}/users/{id}/edit`              | `UserController@edit`                  | Edit user form        |
| `PUT`    | `/{locale}/users/{id}`                   | `UserController@update`                | Update user           |
| `DELETE` | `/{locale}/users/{id}`                   | `UserController@destroy`               | Delete user           |
| `GET`    | `/{locale}/notifications`                | `NotificationController@index`         | List notifications    |
| `GET`    | `/{locale}/notifications/{id}`           | `NotificationController@show`          | View notification     |
| `POST`   | `/{locale}/notifications/mark-all-read`  | `NotificationController@markAllAsRead` | Mark all as read      |
| `POST`   | `/{locale}/notifications/{id}/mark-read` | `NotificationController@markAsRead`    | Mark single as read   |
| `DELETE` | `/{locale}/notifications/{id}`           | `NotificationController@destroy`       | Delete notification   |
