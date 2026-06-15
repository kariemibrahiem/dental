# AI Dental

Laravel 11 backend for the AI Dental clinic app. This project now includes a complete clinic-facing REST API for login, dashboard, doctors, patients, and users, plus the existing patient/doctor scan and report APIs.

## Stack

- PHP 8.3+
- Laravel 11
- Laravel Sanctum
- MySQL

## Main API Features

- Clinic login with `name`, `email`, or `phone`
- Dashboard stats for the mobile dashboard
- Full REST APIs for `doctors`, `patients`, and `users`
- Doctor and patient authentication APIs
- Patient scan upload and report APIs
- Doctor scan review API
- Case result lookup API for frontend dropdowns

## Case Results

The clinic frontend uses these simple case groups:

- `Healthy`
- `Cavity`
- `Infection`

The backend also supports older detailed AI labels such as `Caries`, `Calculus`, `Gingivitis`, `Hypodontia`, `Tooth Discoloration`, and `Ulcers`. Dashboard and patient APIs now normalize those older labels into the 3 frontend groups.

## Important Fixes Included

- Added missing clinic auth API: `POST /api/v1/auth/login`
- Added full REST APIs for doctors, patients, and users
- Added `GET /api/v1/dashboard`
- Added `GET /api/v1/lookups/case-results`
- Fixed doctor/patient creation when frontend only sends `name`, `doctor`, and `result`
- Added generated email/password support for doctor and patient records
- Added migration to expand enum values for `Cavity` and `Infection`
- Fixed the broken `admins` migration `otp` column definition

## Setup

1. Install dependencies:
   `composer install`
2. Copy env and configure MySQL:
   `.env`
3. Run migrations and seed data:
   `php artisan migrate --seed`
4. Link storage:
   `php artisan storage:link`
5. Start the app:
   `php artisan serve`

## Seeded Credentials

- Clinic API user:
  `login: clinic`
  `password: clinic123`
- Clinic API user alternative login:
  `clinic@ai-dental.local`
  `01500000000`
- Web admin:
  `user_name: admin`
  `email: admin@admin.com`
  `password: admin`
- Doctor sample:
  `mostafa@dental.com / doctor123`
- Patient sample:
  `ahmed@gmail.com / patient123`

## API Docs

- Full API reference: [docs/API_DOCUMENTATION.md](/d:/projects/dental/docs/API_DOCUMENTATION.md)
- Postman collection: [postman/AI-Dental.postman_collection.json](/d:/projects/dental/postman/AI-Dental.postman_collection.json)
- Arabic API reference: [docs/API_DOCUMENTATION_AR.md](/d:/projects/dental/docs/API_DOCUMENTATION_AR.md)
- Arabic backend flow: [docs/BACKEND_FLOW_AR.md](/d:/projects/dental/docs/BACKEND_FLOW_AR.md)

## Notes

- The local CLI PHP available in this workspace is `8.1.5`, while this project requires `PHP >= 8.3`. I verified the edited PHP files with `php -l`, but full `artisan` execution must be run in a PHP 8.3 environment.
