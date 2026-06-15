# شرح الـ API بالعربي بشكل بسيط

## الفكرة في سطرين

المشروع فيه 3 أنواع ناس:

- موظف أو مدير الكلينك
- دكتور
- مريض

كل واحد فيهم له APIs خاصة به.  
اليوزر العادي الذي ستبعت له الملف هذا يكفي يعرف: "أنا لو فتحت الشاشة الفلانية، التطبيق يكلم أنهي API؟ ويرسل له ايه؟"

## البداية

كل الروابط تبدأ من:

`{{baseUrl}}/api/v1`

مثال:

`http://127.0.0.1:8000/api/v1`

## شكل الرد

أي API غالبًا سترجع:

```json
{
  "status": "success",
  "message": "رسالة توضح النتيجة",
  "data": {}
}
```

ولو فيه مشكلة:

```json
{
  "status": "error",
  "message": "سبب المشكلة",
  "data": []
}
```

## أول حاجة: APIs العامة

هذه لا تحتاج Token.

### `POST /auth/login`

هذه API دخول موظف أو مدير الكلينك.

ترسل:

```json
{
  "login": "clinic",
  "password": "clinic123"
}
```

الـ `login` ينفع يكون اسم أو إيميل أو رقم موبايل.  
ترجع بيانات المستخدم + token.

### `POST /auth/doctor/login`

هذه API دخول الدكتور.

ترسل:

```json
{
  "email": "mostafa@dental.com",
  "password": "doctor123"
}
```

ترجع بيانات الدكتور + token.

### `POST /auth/patient/login`

هذه API دخول المريض.

ترسل:

```json
{
  "email": "ahmed@gmail.com",
  "password": "patient123"
}
```

ترجع بيانات المريض + الدكتور المرتبط به + token.

### `POST /auth/patient/register`

هذه API تسجيل مريض جديد.

ترسل:

```json
{
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "phone": "01000000000",
  "password": "patient123",
  "doctor_id": 1
}
```

بعد التسجيل، المريض يأخذ token مباشرة.

### `GET /specialties`

تجلب تخصصات الدكاترة، لكي تظهر في أي قائمة اختيار تخص التخصص.

### `GET /lookups/case-results`

ترجع القيم البسيطة التي يستخدمها الفرونت:

- `Healthy`
- `Cavity`
- `Infection`

---

## ثاني حاجة: APIs موظف أو مدير الكلينك

كل APIs هنا تحتاج Token من `POST /auth/login`.

الهيدر:

`Authorization: Bearer TOKEN`

### `GET /auth/me`

تجلب بيانات المستخدم الحالي بعد تسجيل الدخول.

### `POST /auth/logout`

تعمل Logout وتحذف التوكن الحالي.

### `GET /dashboard`

هذه أهم API في النظام.  
هي التي ترجع بيانات الداشبورد الرئيسية.

ترجع باختصار:

- عدد المرضى
- عدد الدكاترة
- عدد الحالات `Healthy`
- عدد الحالات `Cavity`
- عدد الحالات `Infection`
- شارت المرضى اليومي
- شارت توزيع الحالات
- المرضى على كل دكتور
- التنبيهات
- آخر النشاطات

---

## شاشة الدكاترة

### `GET /doctors`

تجلب كل الدكاترة.

### `POST /doctors`

تضيف دكتور جديد.

أبسط شكل للإرسال:

```json
{
  "name": "Mostafa"
}
```

ولو تحب ترسل بيانات كاملة:

```json
{
  "name": "Mostafa",
  "email": "mostafa@clinic.com",
  "phone": "01011111111",
  "password": "doctor123",
  "specialty_ids": [1, 2]
}
```

مهم:

- لو الإيميل غير موجود، الباك يولده وحده
- لو الباسورد غير موجود، الباك يولده وحده
- القيم المتولدة ترجع في `generated_credentials`

### `GET /doctors/{id}`

تجلب دكتور واحد بالتفصيل.

### `PUT /doctors/{id}`

تعدل دكتور موجود.

مثال:

```json
{
  "name": "Mostafa Updated",
  "phone": "01011111111"
}
```

### `DELETE /doctors/{id}`

تحذف دكتور.

---

## شاشة المرضى

### `GET /patients`

تجلب كل المرضى مع اسم الدكتور المرتبط بكل مريض.

### `POST /patients`

تضيف مريض جديد.

أبسط شكل:

```json
{
  "name": "Ahmed",
  "doctor_id": 1,
  "result": "Healthy"
}
```

شكل أكبر لو تحب:

```json
{
  "name": "Ahmed",
  "doctor_id": 1,
  "result": "Infection",
  "date": "2026-06-15",
  "email": "ahmed@clinic.com",
  "phone": "01111111111",
  "password": "patient123"
}
```

مهم:

- لو الإيميل غير موجود، الباك يولده
- لو الباسورد غير موجود، الباك يولده
- لو النتيجة من النوع القديم، الباك يحولها للشكل البسيط المفهوم للفرونت

### `GET /patients/{id}`

تجلب مريض واحد بالتفصيل.

### `PUT /patients/{id}`

تعدل مريض موجود.

مثال:

```json
{
  "doctor_id": 2,
  "result": "Cavity",
  "date": "2026-06-15"
}
```

### `DELETE /patients/{id}`

تحذف مريض.

---

## شاشة المستخدمين الداخليين

### `GET /users`

تجلب كل المستخدمين الداخليين للنظام.

### `POST /users`

تضيف User جديد.

ترسل:

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

تجلب User واحد.

### `PUT /users/{id}`

تعدل User موجود.

### `DELETE /users/{id}`

تحذف User.

---

## ثالث حاجة: APIs المريض

هذه تحتاج Patient Token.

### `GET /patient/profile`

تجلب بروفايل المريض الحالي.

### `GET /patient/dashboard`

تجلب داشبورد المريض.

ترجع باختصار:

- عدد الـ scans
- عدد الحالات السليمة
- عدد حالات `Cavity`
- عدد حالات `Infection`
- قائمة الـ scans
- بيانات الدكتور

### `POST /patient/scans/upload`

هذه API المريض يرفع بها صورة السن أو الأشعة للتحليل.

الإرسال يكون `form-data`.

المطلوب:

- `image`

النتيجة:

- الصورة تتخزن
- التحليل يتم
- يسجل `scan` جديد
- تتحدث نتيجة المريض

### `GET /patient/reports`

تجلب كل تقارير المريض.

### `POST /patient/reports`

يرفع تقرير جديد.

الإرسال `form-data`.

المطلوب:

- `title`
- `description`
- `image`

### `GET /patient/reports/{id}`

تجلب تقرير واحد.

---

## رابع حاجة: APIs الدكتور

هذه تحتاج Doctor Token.

### `GET /doctor/profile`

تجلب بروفايل الدكتور الحالي.

### `GET /doctor/dashboard`

تجلب داشبورد الدكتور.

ترجع باختصار:

- عدد المرضى المرتبطين به
- عدد الـ scans التي تنتظر مراجعة
- قائمة المرضى
- قائمة الـ pending scans

### `POST /doctor/scans/{id}/review`

هذه API الدكتور يراجع بها scan.

يرسل:

```json
{
  "notes": "Needs follow-up check",
  "override_result": "Cavity"
}
```

المعنى ببساطة:

- الدكتور يكتب ملاحظاته
- ولو يحب يغير النتيجة يرسل `override_result`
- بعدها الـ scan يتحول إلى `reviewed`

---

## الخلاصة السريعة

لو المستخدم سأل:

"أنا كموظف أحتاج ايه؟"

الإجابة:

- Login
- Dashboard
- Doctors APIs
- Patients APIs
- Users APIs

"أنا كمريض أحتاج ايه؟"

- Login أو Register
- Profile
- Dashboard
- Upload Scan
- Reports

"أنا كدكتور أحتاج ايه؟"

- Login
- Profile
- Dashboard
- Review Scan

## أهم ملاحظة للفرونت

أفضل 3 قيم يتعامل بها الفرونت هي:

- `Healthy`
- `Cavity`
- `Infection`

وده يكفي جدًا لعرض الحالات بشكل واضح وسهل.
