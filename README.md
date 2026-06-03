# HosCare HMS

**HosCare** is a comprehensive Hospital Management System (HMS) built for Tanzanian healthcare facilities. It manages the full patient lifecycle — from registration and clinical consultation to laboratory, pharmacy dispensing, billing, and NHIF insurance claims submission — all in a single, role-based web application.

---

## Features

### Patient Management
- Patient registration with demographics and file number
- Attendance/visit tracking (outpatient & inpatient)
- Vital signs recording
- Allergy and medical history logging
- Emergency patient handling

### Clinical Workflow
- Doctor consultation and clinical notes
- Diagnosis management with ICD-10 codes
- Investigation and lab test requests
- Treatment planning and prescription

### Laboratory
- Sample registration and result entry
- Lab test catalogue management
- Lab report generation

### Pharmacy
- Drug/medicine stock management
- Prescription dispensing for regular and emergency patients
- Inventory tracking and zero-stock alerts
- Pharmacy reports

### Billing & Payments
- Itemised bill generation per attendance
- Payment recording and receipt printing
- Bill editing and adjustments
- Payment reports and income statements

### NHIF Insurance Integration
Full integration with the Tanzania National Health Insurance Fund (NHIF) APIs:
- **Card verification** — verify member eligibility and coverage in real time
- **Service approvals** — request and track NHIF service authorizations
- **Inpatient admissions** — admit, transfer, and discharge patients through NHIF
- **Referrals** — create and acknowledge service/treatment referrals
- **Pre-approvals** — request pre-authorization for high-cost services
- **Claims (OCS)** — submit individual folios and monthly claim batches
- **Bill confirmation** — OTP-based patient bill confirmation
- **Price lists** — pull NHIF package pricing and excluded services
- **Practitioner attendance** — log practitioner sessions biometrically

### Financial Management
- Expense ledger and transaction tracking
- Expense reports and income statements
- Invoice management

### Administration & Reports
- Role-based user access control
- Ward and room management
- Insurance scheme configuration
- Procedure catalogue
- Sales, inventory, lab, and attendance reports
- Doctor performance tracking

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.3+ |
| Database | MySQL / MariaDB |
| Frontend | Blade templates, Bootstrap, jQuery |
| DataTables | Yajra Laravel DataTables 13 |
| HTTP Client | Laravel `Http` facade (Guzzle) |
| Auth | Session-based with role/level guards |
| Cache | File cache (token management) |
| Build tool | Vite |

---

## Requirements

- PHP >= 8.3
- Composer
- Node.js >= 18 & npm
- MySQL 8+ or MariaDB 10.6+
- A web server (Nginx / Apache) or `php artisan serve` for local development

---

## Installation

```bash
# 1. Clone the repository
git clone <repository-url> hoscare-hms
cd hoscare-hms

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install && npm run build

# 4. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 5. Configure your database in .env
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Run migrations and seed the database
php artisan migrate --seed

# 7. Start the development server
php artisan serve
```

---

## NHIF Integration Setup

Add the following variables to your `.env` file. Credentials are issued by NHIF Tanzania through their facility registration portal.

```env
# Use https://test.nhif.or.tz for sandbox, https://nhif.or.tz for production
NHIF_BASE_URL=https://test.nhif.or.tz
NHIF_AUTH_URL=https://test.nhif.or.tz/authserver/connect/token
NHIF_CLIENT_ID=your_client_id
NHIF_CLIENT_SECRET=your_client_secret
NHIF_SCOPE=OnlineServices
NHIF_FACILITY_CODE=your_facility_code
```

The integration covers two NHIF services:

| Service | Base URL | Purpose |
|---|---|---|
| ServiceHub | `/servicehub` | Verification, approvals, admissions, referrals |
| OCS | `/ocs` | Online claims submission and pricing |

All NHIF routes are available under `/nhif/*` and are protected by the application's authentication middleware.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/          # Feature controllers (Patient, Payment, Pharmacy, Nhif, …)
│   └── Middleware/
├── Models/                   # Eloquent models (Patient, Insurance, PatientAttendance, …)
├── Services/
│   └── Nhif/                 # NHIF service layer
│       ├── NhifAuthService.php       # Token management with caching
│       ├── NhifServiceHubService.php # ServiceHub API wrapper
│       └── NhifOcsService.php        # OCS claims API wrapper
└── Providers/
config/
├── services.php              # Third-party service config (includes NHIF)
routes/
└── web.php                   # All application routes including /nhif/* group
```

---

## User Roles

The system uses a level + role-based access model:

| Level | Role | Access |
|---|---|---|
| 1 | Admin | Full system access |
| 0 | Staff | Role-restricted access (doctor, nurse, pharmacist, lab, finance, etc.) |

---

## Key Workflows

### Outpatient Visit
1. Receptionist registers/searches patient → starts attendance
2. Nurse records vitals and allergies
3. Doctor writes clinical notes, orders investigations, writes prescription
4. Lab technician registers sample and enters results
5. Pharmacist dispenses medication
6. Cashier generates bill and records payment
7. (NHIF patients) Bill confirmation OTP sent → folio submitted to OCS

### NHIF Claim Submission
1. Verify patient card → `GET /nhif/verify-card`
2. Record services rendered during visit
3. Request bill confirmation OTP → `POST /nhif/request-bill-confirmation`
4. Sign and submit folio → `POST /nhif/sign-folio` → `POST /nhif/submit-folio`
5. End of month: batch submit → `POST /nhif/submit-monthly-claim`

---

## License

This project is proprietary software. All rights reserved.
