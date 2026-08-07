# Technical Assessment - Invoices, Vehicle Gate Operations & M-Pesa C2B API

This repository contains the completed technical assessment built with **Laravel 12**, **Filament PHP (v5.7)**, and a custom **M-Pesa C2B API Callback Handler**.

---

## Technical Stack & Requirements

* **PHP:** ^8.2
* **Framework:** Laravel 12.x
* **Admin Panel:** Filament  v5.7.5
* **Database:** Microsoft Sql
* **API Engine:** REST (JSON Payload handling)

---

## Features & Completed Tasks

### Task 1: Invoices Resource (Filament Panel)
* **Database Architecture:** Models, Migrations, Factories, and Seeders for Customer Invoices.
* **Management UI:** Full CRUD interface for creating invoices

### Task 2: Vehicle Gate Operations (Filament Panel)
* **Database Architecture:** Models, Migrations, Factories, and Seeders for `Vehicles`, `Drivers`, `Customers`, and `VehicleGateLogs`.
* **Dynamic Form Auto-Population:** Automatically retrieves and populates the Driver's National ID and Phone Number upon selecting a driver during **Gate In**.
* **Audit Trails:** System automatically captures `gated_in_at`, `gated_in_by` (authenticated user), `gated_out_at`, and `gated_out_by`.
* **Gate Out Workflow:** Table row actions and a dedicated top-header action to safely transition vehicles marked as `GATED_IN` to `GATED_OUT`.

### Task 3: M-Pesa C2B API Callback
* **REST Endpoint:** `POST /api/mpesa/c2b/callback`
* **Data Persistence:** Extracts all callback fields (`TransID`, `TransAmount`, `MSISDN`, `BillRefNumber`, `FirstName`, `LastName`, etc.) into individual string columns in the `mpesa_c2b_transactions` table.
* **Safaricom Compliant Response:** Returns structured JSON acknowledgment (`ResultCode: 0`, `ResultDesc: "Accepted"`).

---

## Quick Setup Instructions

```bash
# 1. Clone repository & install dependencies
git clone [https://github.com/jimmeekae/technical-assessment.git](https://github.com/jimmeekae/technical-assessment.git)
cd technical-assessment
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set your db credentials

# 4. Run database migrations and seed default data
php artisan migrate --seed

# 5. Start local development server
php artisan serve


## Admin Credentials & Dashboard Access

* **URL:** `http://127.0.0.1:8000/admin`
* **Email:** `test@example.com`
* **Password:** `password`

---

## Testing M-Pesa C2B Callback API

Send a `POST` request to `http://127.0.0.1:8000/api/mpesa/c2b/callback` via Postman, cURL, or HTTPie.

### Request Headers
* `Content-Type: application/json`
* `Accept: application/json`

### Sample Request Payload
```json
{
    "TransactionType": "Pay Bill",
    "TransID": "RKT1234567",
    "TransTime": "20260806161400",
    "TransAmount": "1500.00",
    "BusinessShortCode": "600000",
    "BillRefNumber": "INV-1002",
    "InvoiceNumber": "INV-1002",
    "OrgAccountBalance": "45000.00",
    "ThirdPartyTransID": "",
    "MSISDN": "254712345678",
    "FirstName": "John",
    "MiddleName": "K",
    "LastName": "Doe"
}

### Expected Response (200 OK)
```json
{
    "ResultCode": 0,
    "ResultDesc": "Accepted",
    "ThirdPartyTransID": "RKT1234567"
}