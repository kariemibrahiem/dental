# شرح الباك كحكاية بسيطة

## الحكاية من أولها

عندنا في المشروع 3 أبطال:

- موظف الكلينك
- الدكتور
- المريض

كل واحد فيهم يدخل من باب مختلف، لكن في النهاية كلهم يصبوا في نفس قاعدة البيانات ونفس الداشبورد.

---

## القصة 1: موظف الكلينك يفتح النظام

أول ما الموظف يفتح التطبيق، يعمل Login.

الطلب يذهب إلى:

- `POST /api/v1/auth/login`

الملف المسؤول:

- [ClinicAuthController.php](/d:/projects/dental/app/Http/Controllers/v1/ClinicAuthController.php)

الذي يحدث:

1. الباك يبحث عن المستخدم.
2. يتأكد من الباسورد.
3. يعطيه token.

وبعدها هذا الموظف يقدر يفتح:

- الداشبورد
- شاشة الدكاترة
- شاشة المرضى
- شاشة المستخدمين الداخليين

---

## القصة 2: الداشبورد الرئيسية تشتغل

بعد الـ Login، الفرونت يطلب الداشبورد.

الطلب يذهب إلى:

- `GET /api/v1/dashboard`

الملفات المسؤولة:

- [DashboardController.php](/d:/projects/dental/app/Http/Controllers/v1/DashboardController.php)
- [DashboardService.php](/d:/projects/dental/app/Services/Admin/DashboardService.php)

هنا الباك يجمع كل شيء في رد واحد:

- عدد المرضى
- عدد الدكاترة
- عدد الحالات السليمة
- عدد حالات التسوس أو التجاويف
- عدد حالات العدوى أو الحالات الحرجة
- الشارتات
- التنبيهات
- آخر النشاطات

بمعنى أبسط:

الداشبورد لا تخزن بيانات جديدة، هي فقط "تلملم" الموجود وتعرضه بشكل مرتب.

---

## القصة 3: الموظف يضيف دكتور

الموظف يفتح شاشة الدكاترة ويضغط إضافة.

الطلب يذهب إلى:

- `POST /api/v1/doctors`

الملف المسؤول:

- [DoctorController.php](/d:/projects/dental/app/Http/Controllers/v1/DoctorController.php)

ولو الإيميل أو الباسورد لم يرسلهم الفرونت، الباك يولدهم من هنا:

- [IdentityGenerator.php](/d:/projects/dental/app/Support/IdentityGenerator.php)

الذي يحدث:

1. الباك يحفظ الدكتور.
2. يربطه بالتخصصات لو موجودة.
3. يسجل Activity.

بعدها يظهر الدكتور في:

- قائمة الدكاترة
- اختيار الدكتور داخل شاشة المرضى
- إحصائيات الدكاترة في الداشبورد

---

## القصة 4: الموظف يضيف مريض

الموظف يفتح شاشة المرضى ويضيف مريض.

الطلب يذهب إلى:

- `POST /api/v1/patients`

الملف المسؤول:

- [PatientController.php](/d:/projects/dental/app/Http/Controllers/v1/PatientController.php)

ولو النتيجة مكتوبة بشكل قديم أو تفصيلي، الباك يبسطها من هنا:

- [DentalCaseCatalog.php](/d:/projects/dental/app/Support/DentalCaseCatalog.php)

ولو الإيميل أو الباسورد غير موجودين، تتولد من هنا:

- [IdentityGenerator.php](/d:/projects/dental/app/Support/IdentityGenerator.php)

الذي يحدث:

1. المريض يتسجل في قاعدة البيانات.
2. يتربط بدكتور.
3. تتحدد حالته.
4. يتسجل Activity.

بعدها يظهر المريض في:

- قائمة المرضى
- أرقام الداشبورد
- الشارتات
- التنبيهات لو حالته ليست `Healthy`

---

## القصة 5: من أين تأتي أرقام الداشبورد؟

هذا الجزء مهم جدًا.

الملف الذي يحسب أغلب أرقام الداشبورد هو:

- [DashboardService.php](/d:/projects/dental/app/Services/Admin/DashboardService.php)

هو الذي يحدد:

- `Total Patients`
  من عدد المرضى
- `Doctors`
  من عدد الدكاترة
- `Healthy`
  من المرضى السليمين
- `Cavity`
  من الحالات التي تعتبر تسوس أو تجويف
- `Infection`
  من الحالات التي تعتبر عدوى أو حالة غير سليمة
- `Daily Patients`
  من تاريخ المرضى
- `Patients by Doctor`
  من عدد المرضى على كل دكتور
- `Alerts`
  من الحالات غير السليمة
- `Recent Activity`
  من جدول النشاطات

ببساطة:

أي إضافة أو تعديل على مريض أو دكتور، غالبًا ستنعكس هنا.

---

## القصة 6: المريض يدخل النظام

المريض عنده طريقين:

- تسجيل جديد
- أو Login

التسجيل:

- `POST /api/v1/auth/patient/register`

تسجيل الدخول:

- `POST /api/v1/auth/patient/login`

الملف المسؤول:

- [PatientAuthController.php](/d:/projects/dental/app/Http/Controllers/v1/PatientAuthController.php)

بعدها المريض يقدر يفتح:

- البروفايل
- داشبورد المريض
- رفع scan
- رفع report

---

## القصة 7: المريض يرفع Scan

هذه من أهم القصص في المشروع.

المريض يرسل صورة سن أو أشعة.

الطلب يذهب إلى:

- `POST /api/v1/patient/scans/upload`

الملف المسؤول:

- [PatientDashboardController.php](/d:/projects/dental/app/Http/Controllers/v1/PatientDashboardController.php)

وملف الذكاء أو التحليل:

- [dental_predict.py](/d:/projects/dental/dental_predict.py)

الذي يحدث:

1. الصورة تتخزن.
2. الباك يحاول تحليلها بالـ Python.
3. لو التحليل لم يعمل، يستخدم fallback داخل PHP.
4. يسجل Scan جديد.
5. يحدث نتيجة المريض الأساسية.
6. يسجل Activity.

بعدها هذه النتيجة تظهر في 3 أماكن:

- عند المريض في Dashboard الخاص به
- عند الدكتور في قائمة الـ pending scans
- في Dashboard الكلينك ضمن الأرقام والتنبيهات

---

## القصة 8: الدكتور يدخل ويراجع الـ Scan

الدكتور يعمل Login من:

- `POST /api/v1/auth/doctor/login`

الملف المسؤول:

- [DoctorAuthController.php](/d:/projects/dental/app/Http/Controllers/v1/DoctorAuthController.php)

ثم يفتح Dashboard الدكتور من:

- `GET /api/v1/doctor/dashboard`

الملف المسؤول:

- [DoctorDashboardController.php](/d:/projects/dental/app/Http/Controllers/v1/DoctorDashboardController.php)

هنا الدكتور يرى:

- المرضى التابعين له
- عدد الـ scans التي تنتظر مراجعة
- قائمة الـ pending scans

ولو الدكتور راجع scan، يستخدم:

- `POST /api/v1/doctor/scans/{id}/review`

في نفس الملف:

- [DoctorDashboardController.php](/d:/projects/dental/app/Http/Controllers/v1/DoctorDashboardController.php)

الذي يحدث:

1. يكتب ملاحظاته.
2. لو يريد تغيير النتيجة، يرسل نتيجة جديدة.
3. الـ scan يتحول إلى `reviewed`.
4. نتيجة المريض قد تتحدث.
5. يسجل Activity.

بعدها النتيجة تظهر في:

- Dashboard الدكتور
- Dashboard المريض
- Dashboard الكلينك

---

## القصة 9: المريض يرفع Report

المريض يقدر يرفع تقرير طبي أو صورة تقرير.

الطلبات:

- `GET /api/v1/patient/reports`
- `POST /api/v1/patient/reports`
- `GET /api/v1/patient/reports/{id}`

الملف المسؤول:

- [ReportController.php](/d:/projects/dental/app/Http/Controllers/v1/ReportController.php)

الذي يحدث:

1. التقرير يتخزن.
2. يرتبط بالمريض نفسه.
3. يظل ظاهرًا في قائمة تقارير المريض.

هذا الجزء حاليًا خاص بالمريض أكثر، وليس عنصرًا رئيسيًا داخل Dashboard الكلينك.

---

## القصة 10: إدارة المستخدمين الداخليين

لو صاحب النظام يريد إضافة موظف استقبال أو User جديد، يستخدم:

- `GET /api/v1/users`
- `POST /api/v1/users`
- `PUT /api/v1/users/{id}`
- `DELETE /api/v1/users/{id}`

الملف المسؤول:

- [ClinicUserController.php](/d:/projects/dental/app/Http/Controllers/v1/ClinicUserController.php)

ولو احتاج الباك أن يولد `code` تلقائيًا، يستخدم:

- [IdentityGenerator.php](/d:/projects/dental/app/Support/IdentityGenerator.php)

هذا الجزء يفيد الإدارة فقط، ولا يظهر كأرقام مباشرة في الداشبورد.

---

## القصة الأخيرة: أهم معلومة للفرونت

الفرونت الأفضل له يعيش على 3 كلمات فقط:

- `Healthy`
- `Cavity`
- `Infection`

ولو التحليل رجع مسميات كثيرة أو قديمة، الباك يجمعها ويبسطها من خلال:

- [DentalCaseCatalog.php](/d:/projects/dental/app/Support/DentalCaseCatalog.php)

وده سبب إن الواجهة تظل بسيطة ومفهومة للمستخدم.

## الخلاصة

لو أردنا أن نحكي المشروع في جملة واحدة:

"الإدارة تضيف دكاترة ومرضى، المريض يرفع scan، الدكتور يراجعه، والداشبورد تجمع كل هذا وتعرضه بشكل سهل."
