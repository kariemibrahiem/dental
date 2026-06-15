# AI Dental API Documentation

## Base URL

`{{baseUrl}}/api/v1`

## Response Format

All API endpoints use the same envelope:

```json
{
  "status": "success",
  "message": "Human readable message",
  "data": {}
}
```

## Authentication

Clinic management routes use Sanctum bearer tokens.

Header:

```http
Authorization: Bearer YOUR_TOKEN
```

## Public Endpoints

### `POST /auth/login`

Clinic user login.

Body:

```json
{
  "login": "clinic",
  "password": "clinic123"
}
```

`login` can be the clinic user's `name`, `email`, or `phone`.

### `POST /auth/doctor/login`

Doctor login.

Body:

```json
{
  "email": "mostafa@dental.com",
  "password": "doctor123"
}
```

### `POST /auth/patient/login`

Patient login.

Body:

```json
{
  "email": "ahmed@gmail.com",
  "password": "patient123"
}
```

### `POST /auth/patient/register`

Patient self registration.

Body:

```json
{
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "phone": "01000000000",
  "password": "patient123",
  "doctor_id": 1
}
```

### `GET /specialties`

Returns all dental specialties.

### `GET /lookups/case-results`

Returns frontend case categories:

```json
[
  "Healthy",
  "Cavity",
  "Infection"
]
```

## Clinic Protected Endpoints

These routes require a clinic user token from `POST /auth/login`.

### `POST /auth/logout`

Logs out the current clinic token.

### `GET /auth/me`

Returns the current clinic user.

### `GET /dashboard`

Returns the clinic dashboard data used by the mobile frontend.

Main keys:

- `stats`
- `total_patients`
- `total_doctors`
- `healthy_count`
- `cavity_count`
- `infection_count`
- `charts.daily_patients`
- `charts.cases_distribution`
- `charts.patients_by_doctor`
- `alerts`
- `recent_activities`
- `doctor_statistics`

## Doctors REST API

### `GET /doctors`

Returns all doctors.

### `POST /doctors`

Creates a doctor.

Body:

```json
{
  "name": "Mostafa"
}
```

Optional fields:

- `email`
- `phone`
- `password`
- `specialty_ids`

If `email` or `password` is missing, the API generates them and returns them in `data.generated_credentials`.

### `GET /doctors/{id}`

Returns one doctor.

### `PUT /doctors/{id}`

Updates a doctor.

Body example:

```json
{
  "name": "Mostafa Updated",
  "phone": "01011111111"
}
```

### `DELETE /doctors/{id}`

Deletes a doctor.

## Patients REST API

### `GET /patients`

Returns all patients with doctor info.

### `POST /patients`

Creates a patient.

Body:

```json
{
  "name": "Ahmed",
  "doctor_id": 1,
  "result": "Healthy"
}
```

Optional fields:

- `email`
- `phone`
- `password`
- `date`

Accepted `result` values:

- `Healthy`
- `Cavity`
- `Infection`
- older detailed AI labels are also accepted and normalized

If `email` or `password` is missing, the API generates them and returns them in `data.generated_credentials`.

### `GET /patients/{id}`

Returns one patient.

### `PUT /patients/{id}`

Updates a patient.

Body example:

```json
{
  "doctor_id": 2,
  "result": "Infection",
  "date": "2026-06-14"
}
```

### `DELETE /patients/{id}`

Deletes a patient.

## Users REST API

### `GET /users`

Returns clinic users.

### `POST /users`

Creates a clinic user.

Body:

```json
{
  "name": "Reception",
  "phone": "01512345678",
  "email": "reception@clinic.com",
  "password": "secret123",
  "status": true
}
```

### `GET /users/{id}`

Returns one clinic user.

### `PUT /users/{id}`

Updates a clinic user.

### `DELETE /users/{id}`

Deletes a clinic user.

## Patient Protected Endpoints

These routes require a patient token from `POST /auth/patient/login` or `POST /auth/patient/register`.

### `GET /patient/profile`

Returns the authenticated patient profile.

### `GET /patient/dashboard`

Returns:

- `stats.total_scans`
- `stats.healthy_scans`
- `stats.cavity_scans`
- `stats.infection_scans`
- `stats.risk_scans`
- `scans`
- `doctor`

### `POST /patient/scans/upload`

Uploads a scan image for AI analysis.

Form-data:

- `image` file

### `GET /patient/reports`

Returns patient reports.

### `POST /patient/reports`

Uploads a medical report.

Form-data:

- `title`
- `description`
- `image` file

### `GET /patient/reports/{id}`

Returns one patient report.

## Doctor Protected Endpoints

These routes require a doctor token from `POST /auth/doctor/login`.

### `GET /doctor/profile`

Returns the authenticated doctor profile.

### `GET /doctor/dashboard`

Returns doctor dashboard data with:

- `stats.assigned_patients`
- `stats.pending_reviews`
- `patients`
- `pending_scans`

### `POST /doctor/scans/{id}/review`

Reviews a patient scan.

Body:

```json
{
  "notes": "Needs follow-up check",
  "override_result": "Cavity"
}
```

## Frontend Mapping

Mobile screens in this project map cleanly to these endpoints:

- Login screen: `POST /auth/login`
- Dashboard screen: `GET /dashboard`
- Doctors screen:
  `GET /doctors`
  `POST /doctors`
  `DELETE /doctors/{id}`
- Patients screen:
  `GET /patients`
  `POST /patients`
  `DELETE /patients/{id}`

## Postman

Use the included collection:

- [postman/AI-Dental.postman_collection.json](/d:/projects/dental/postman/AI-Dental.postman_collection.json)

It uses only one variable:

- `{{baseUrl}}`

Example:

`http://127.0.0.1:8000`
