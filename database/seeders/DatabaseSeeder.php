<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Scan;
use App\Models\Report;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Spatie permissions seeders
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);
     
        // 1. Create Web Panel Admin account
        $admin = Admin::updateOrCreate(
            ["email" => "admin@admin.com"],
            [
                "user_name" => "admin",
                "password" => "admin",
                "code" => "admin",
                "phone" => "01000000000",
                "image" => "testImage"
            ]
        );

        $admin->syncRoles(["super_admin"]);

        User::updateOrCreate(
            ['phone' => '01500000000'],
            [
                'name' => 'clinic',
                'email' => 'clinic@ai-dental.local',
                'password' => 'clinic123',
                'code' => Str::upper(Str::random(10)),
                'status' => 1,
            ]
        );

        // 2. Seed Dental Specialties
        $ortho = Specialty::create(['name' => 'Orthodontics']);
        $endo = Specialty::create(['name' => 'Endodontics']);
        $surgery = Specialty::create(['name' => 'Oral Surgery']);
        $pediatric = Specialty::create(['name' => 'Pediatric Dentistry']);

        // 3. Seed Doctors with authentication credentials & specialties
        $docMostafa = Doctor::create([
            'name' => 'Mostafa',
            'email' => 'mostafa@dental.com',
            'phone' => '01011111111',
            'password' => 'doctor123', // Encrypted by setPasswordAttribute
        ]);
        $docMostafa->specialties()->attach([$ortho->id, $surgery->id]);

        $docMotaz = Doctor::create([
            'name' => 'Motaz',
            'email' => 'motaz@dental.com',
            'phone' => '01022222222',
            'password' => 'doctor123',
        ]);
        $docMotaz->specialties()->attach([$endo->id, $pediatric->id]);

        // 4. Seed Patients with authentication credentials & links to doctors
        $today = Carbon::today();

        $patientAhmed = Patient::create([
            'name' => 'Ahmed',
            'email' => 'ahmed@gmail.com',
            'phone' => '01111111111',
            'password' => 'patient123',
            'doctor_id' => $docMostafa->id,
            'result' => 'Healthy',
            'date' => $today->copy()->subDays(4)->toDateString(),
        ]);

        $patientSara = Patient::create([
            'name' => 'Sara',
            'email' => 'sara@gmail.com',
            'phone' => '01122222222',
            'password' => 'patient123',
            'doctor_id' => $docMotaz->id,
            'result' => 'Caries',
            'date' => $today->copy()->subDays(4)->toDateString(),
        ]);

        $patientAli = Patient::create([
            'name' => 'Ali',
            'email' => 'ali@gmail.com',
            'phone' => '01133333333',
            'password' => 'patient123',
            'doctor_id' => $docMostafa->id,
            'result' => 'Gingivitis',
            'date' => $today->copy()->subDays(3)->toDateString(),
        ]);

        $patientKareem = Patient::create([
            'name' => 'Kareem',
            'email' => 'kareem@gmail.com',
            'phone' => '01144444444',
            'password' => 'patient123',
            'doctor_id' => $docMostafa->id,
            'result' => 'Calculus',
            'date' => $today->copy()->subDays(3)->toDateString(),
        ]);

        $patientFatma = Patient::create([
            'name' => 'Fatma',
            'email' => 'fatma@gmail.com',
            'phone' => '01155555555',
            'password' => 'patient123',
            'doctor_id' => $docMotaz->id,
            'result' => 'Caries',
            'date' => $today->copy()->subDays(2)->toDateString(),
        ]);

        $patientMona = Patient::create([
            'name' => 'Mona',
            'email' => 'mona@gmail.com',
            'phone' => '01166666666',
            'password' => 'patient123',
            'doctor_id' => $docMotaz->id,
            'result' => 'Healthy',
            'date' => $today->copy()->subDays(2)->toDateString(),
        ]);

        $patientOmar = Patient::create([
            'name' => 'Omar',
            'email' => 'omar@gmail.com',
            'phone' => '01177777777',
            'password' => 'patient123',
            'doctor_id' => $docMostafa->id,
            'result' => 'Ulcers',
            'date' => $today->copy()->subDays(1)->toDateString(),
        ]);

        $patientYasmine = Patient::create([
            'name' => 'Yasmine',
            'email' => 'yasmine@gmail.com',
            'phone' => '01188888888',
            'password' => 'patient123',
            'doctor_id' => $docMotaz->id,
            'result' => 'Tooth Discoloration',
            'date' => $today->copy()->subDays(1)->toDateString(),
        ]);

        $patientZain = Patient::create([
            'name' => 'Zain',
            'email' => 'zain@gmail.com',
            'phone' => '01199999999',
            'password' => 'patient123',
            'doctor_id' => $docMostafa->id,
            'result' => 'Hypodontia',
            'date' => $today->toDateString(),
        ]);

        $patientHassan = Patient::create([
            'name' => 'Hassan',
            'email' => 'hassan@gmail.com',
            'phone' => '01200000000',
            'password' => 'patient123',
            'doctor_id' => $docMotaz->id,
            'result' => 'Ulcers',
            'date' => $today->toDateString(),
        ]);

        // 5. Seed Scans (AI analyses history)
        Scan::create([
            'patient_id' => $patientAhmed->id,
            'image_path' => 'scans/sample_healthy.jpg',
            'ai_result' => 'Healthy',
            'confidence_score' => 97.45,
            'doctor_id' => $docMostafa->id,
            'status' => 'reviewed',
            'notes' => 'Everything looks perfect. Clean tooth structure.',
        ]);

        Scan::create([
            'patient_id' => $patientSara->id,
            'image_path' => 'scans/sample_caries.jpg',
            'ai_result' => 'Caries',
            'confidence_score' => 91.20,
            'doctor_id' => $docMotaz->id,
            'status' => 'pending',
        ]);

        Scan::create([
            'patient_id' => $patientAli->id,
            'image_path' => 'scans/sample_gingivitis.jpg',
            'ai_result' => 'Gingivitis',
            'confidence_score' => 94.60,
            'doctor_id' => $docMostafa->id,
            'status' => 'pending',
        ]);

        // 6. Seed User Medical Reports
        Report::create([
            'patient_id' => $patientAhmed->id,
            'title' => 'Initial Panoramic X-Ray',
            'description' => 'Full panoramic digital scan showing wisdom tooth eruption.',
            'image_path' => 'reports/panoramic_sample.jpg',
        ]);

        Report::create([
            'patient_id' => $patientSara->id,
            'title' => 'Previous Dental History Report',
            'description' => 'Summary of fillings done on lower left molar in 2024.',
            'image_path' => 'reports/history_sample.jpg',
        ]);

        // 7. Seed Dynamic Activities logs
        $activities = [
            ['description' => 'Clinic Specialty Oral Surgery was added', 'type' => 'doctor_added', 'created_at' => $today->copy()->subDays(5)],
            ['description' => 'Doctor Mostafa joined the clinic staff', 'type' => 'doctor_added', 'created_at' => $today->copy()->subDays(4)],
            ['description' => 'Doctor Motaz joined the clinic staff', 'type' => 'doctor_added', 'created_at' => $today->copy()->subDays(4)],
            ['description' => 'Patient Ahmed registered and uploaded panoramic X-Ray', 'type' => 'patient_added', 'created_at' => $today->copy()->subDays(4)],
            ['description' => 'Patient Sara registered for a consultation', 'type' => 'patient_added', 'created_at' => $today->copy()->subDays(4)],
            ['description' => 'AI Scan analyzed for Ali: Gingivitis detected (94.6%)', 'type' => 'patient_added', 'created_at' => $today->copy()->subDays(3)],
            ['description' => 'Patient Kareem scheduled regular checkup', 'type' => 'patient_added', 'created_at' => $today->copy()->subDays(3)],
            ['description' => 'Doctor Mostafa reviewed scan for Patient Ahmed', 'type' => 'doctor_added', 'created_at' => $today->copy()->subDays(2)],
        ];

        foreach ($activities as $act) {
            Activity::create($act);
        }
    }
}
