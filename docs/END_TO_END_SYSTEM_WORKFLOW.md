# Yoshida Motors — End-to-End System Workflow

## Core System Architecture
To ensure seamless maintainability and a unified user experience, the entire Yoshida Motors ecosystem operates on a **Single-App & Single-Web Architecture** powered by Role-Based Access Control (RBAC):

* **One Mobile Application (The App)**: A single app downloaded by everyone. The interface, menus, and features dynamically adapt based on the user's role (e.g., a Regular Customer sees the appraisal submission screen, while an On-site Inspector sees the vehicle checklist screen).
* **One Web Portal (The Web Panel)**: A single centralized website (`yoshida-motors-admin...`). After logging in, the system routes the user to their specific workspace. An Admin sees the master control panel, while a Dealer only sees the marketplace and bidding screens.

---

## Phase 1: Customer Appraisal & Manual Pricing (Current System)

### 1. Customer Action (The App - Customer View)
* **Access**: Users log into the unified mobile app. As standard users, they are routed to the **Customer Dashboard**.
* **Submission**: They fill out vehicle details, upload categorized photos, and submit the appraisal request.
* **Tracking**: They monitor the status (Draft, Submitted, Under Review, Completed, Rejected) directly from their home screen.

### 2. Administrative Review (The Web Panel - Admin View)
* **Access**: Internal staff log into the web panel and are routed to the **Admin Workspace**.
* **Review & Pricing**: The Admin reviews the incoming requests, analyzes the photos, and manually inputs the purchase offer price or a rejection reason.

---

## Phase 2: Dealer Management System (Expansion)

### 1. Dealer Registration
* Dealers apply via the Yoshida Motors public landing page. Once the Admin verifies their business credentials, their account is granted the **"Dealer Role"**.

### 2. Dealer Exploration (The Web Panel - Dealer View)
* **Restricted Access**: Dealers log into the exact same Web Portal URL as the Admins. However, the RBAC system restricts them to the **Dealer Workspace**.
* **Marketplace**: They cannot see administrative tools, customer personal data, or original appraisal prices. They only see a catalog of vehicles ready for purchase, complete with specs and Admin-verified photos.

---

## Phase 3: Bidding & Auction System

### 1. The Admin Process (The Web Panel - Admin View)
* The Admin selects an acquired vehicle and publishes it to the "Auction Pool," setting a specific time window for bidding.

### 2. The Bidding Process (The Web Panel - Dealer View)
* **Blind Bidding**: Dealers view the Auction Pool on their dashboard. They submit their highest "Blind Bid" (meaning they cannot see the bids of other dealers).
* **Winner Selection**: When the time expires, the system automatically locks the bids. The Admin dashboard highlights the highest bidder, and the winning Dealer's dashboard updates to show their won vehicle and pending invoice.

---

## Phase 4: Vehicle Inspection Workflow & Logistics

### 1. Physical Inspection (The App - Inspector View)
* **Inspector Access**: Yoshida Motors ground staff log into the unified Mobile App. Because their account has the **"Inspector Role"**, they bypass the customer screens and are taken directly to the **Inspection Dashboard**.
* **Checklist Execution**: The Inspector uses the app to go through a digital checklist, verifying the car's physical condition against the initial customer photos, and submits the final report.

### 2. Final Handoff (The Web Panel - Admin View)
* The Admin reviews the Inspector's report on the web panel.
* Once payment is cleared and the vehicle is delivered to the winning Dealer, the Admin updates the lifecycle status to **"Sold"**.
