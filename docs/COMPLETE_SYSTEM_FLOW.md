# Yoshida Motors — End-to-End System Workflow

This document outlines the complete operational flow of the Yoshida Motors ecosystem, connecting the **Customer Mobile App** experience with the **Admin Web Dashboard**. It is designed for stakeholders to understand the business process from start to finish without technical jargon.

---

## Phase 1: Customer Onboarding (Mobile App)

The journey begins when a customer downloads the Yoshida Motors mobile application.

### 1. Account Creation & Login
*   **Sign Up/Login**: Customers can sign up or log in using their **Email** or **Google Account**.
*   **Security**: The app ensures a secure session. If a user logs out, they must log in again to access features.
*   **Profile Setup**: Upon first login, users complete their profile with essential details: Name, Address, Gender, and Birth Date. This ensures meaningful data for future transactions.

---

## Phase 2: Appraisal Submission (Customer Action)

The core feature is the vehicle appraisal system, allowing customers to get a price offer for their car.

### 1. Starting a New Request
*   From the **Home Dashboard**, the customer taps **"Start New Appraisal"**.
*   The system checks if their profile is complete before proceeding.

### 2. Step 1: Vehicle Information
*   The customer enters details about their car:
    *   **Brand & Model** (e.g., Toyota Camry)
    *   **Year of Manufacture**
    *   **License Plate** (optional)
    *   **Mileage** (optional)
    *   **Notes**: Any special condition or comments (e.g., "Scratches on bumper").
*   This data is saved locally on their phone as they type.

### 3. Step 2: Vehicle Photos (Crucial Step)
*   The customer must upload at least one photo (maximum 7).
*   **In-App Camera**: They can take photos directly within the app.
*   **Gallery Upload**: Alternatively, they can select existing photos from their phone.
*   **Labeling**: Each photo requires a category label (e.g., "Front View", "Interior") to help the admin assess the condition.

### 4. Step 3: Review & Submit
*   A summary screen displays all entered info and photos.
*   **Save as Draft**: If not ready, the customer can save it as a "Draft" to finish later.
*   **Submit**: Once confirmed, the customer submits the appraisal.
*   **Outcome**: The status changes to **Submitted** (Blue). The customer sees this on their dashboard.

---

## Phase 3: Administrative Review (Admin Web Panel)

Once a customer submits a request, the action shifts to the Yoshida Motors staff using the web-based Admin Panel.

### 1. Notification & Dashboard
*   **Alert**: The dashboard statistics update immediately to show a new "Pending Review".
*   **Dashboard View**: The admin sees the new request in the "Recent Submissions" list or the full "Appraisals" menu.
*   **Status Indicators**: The new request appears with a **Submitted** badge.

### 2. Review Process
*   The admin opens the request details.
*   **Vehicle Data**: They review the car's Brand, Model, Year, and customer notes (ReadOnly).
*   **Photo Inspection**: They inspect the uploaded photos in high resolution to assess the vehicle's condition.
*   **Status Update**: The admin changes the status to **Under Review** (Yellow).
    *   *System Action*: The customer receives a notification on their phone: *"Your appraisal is now being reviewed."*

### 3. Decision Making
The admin has two primary outcomes:

#### Outcome A: Make an Offer (Complete)
*   The admin determines the car's value.
*   They enter the **Offered Purchase Price** (e.g., ¥1,500,000).
*   They can set a **Price Validity Date** (e.g., valid for 7 days).
*   Admin changes status to **Completed** (Green).

#### Outcome B: Reject the Request
*   If the vehicle doesn't meet criteria (e.g., too old, missing info), the admin rejects it.
*   Admin changes status to **Rejected** (Red).
*   **Mandatory Note**: The admin must write a reason (e.g., "Photos are too blurry" or "We do not accept cars older than 2005").

### 4. Completion
*   The admin clicks **"Save Changes"**.
*   The system automatically sends a detailed **Push Notification** to the customer.

---

## Phase 4: Customer Result & Offer (Mobile App)

The customer is alerted to the admin's decision and returns to the app.

### 1. Receiving the Result
*   **Notification**: A message pops up on their phone (e.g., *"Good news! Your vehicle appraisal is complete."* or *"Update on your appraisal"*).
*   **In-App List**: When opening the app, the appraisal card on the Home Screen updates its color and status.

### 2. Viewing the Details
The customer taps "View Details" to see the outcome:

*   **If Offer Made (Completed)**:
    *   They see a large green banner with the **Offered Price** in Yen.
    *   A **"Next Steps"** guide appears, instructing them on how to proceed with the physical handover and payment collection.

*   **If Rejected**:
    *   They see a red banner.
    *   The **Rejection Reason** written by the admin is displayed clearly.
    *   They typically start a new appraisal with better data.

*   **If Under Review**:
    *   They see a yellow banner asking for patience, with an estimated wait time or contact button.

---

## Summary of Status Lifecycle

| Status           | Color  | Who Acts? | Description                                           |
| :--------------- | :----- | :-------- | :---------------------------------------------------- |
| **Draft**        | Gray   | Customer  | Saved on phone, not yet sent to Yoshida Motors.       |
| **Submitted**    | Blue   | Customer  | Sent to system, waiting for Admin to open it.         |
| **Under Review** | Yellow | Admin     | Admin has opened it and is currently assessing value. |
| **Completed**    | Green  | Admin     | **Final Offer Made.** Price is visible to customer.   |
| **Rejected**     | Red    | Admin     | Request denied. Reason provided to customer.          |

---

## System Management (Admin Only)

Beyond individual appraisals, the Admin has tools to manage the ecosystem:

*   **User Management**: View all registered customers. Admins can manually add users or update their details if a customer calls in for support.
*   **Notifications Center**: A global inbox for the admin to track system events and history.
*   **Manual Appraisal Creation**: If a customer walks into the physical shop, an Admin can manually create an appraisal record on their behalf directly from the web panel.
