<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE patients
            MODIFY result ENUM(
                'Healthy',
                'Cavity',
                'Infection',
                'Calculus',
                'Caries',
                'Gingivitis',
                'Hypodontia',
                'Tooth Discoloration',
                'Ulcers'
            ) NULL
        ");

        DB::statement("
            ALTER TABLE scans
            MODIFY ai_result ENUM(
                'Healthy',
                'Cavity',
                'Infection',
                'Calculus',
                'Caries',
                'Gingivitis',
                'Hypodontia',
                'Tooth Discoloration',
                'Ulcers'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('patients')->where('result', 'Cavity')->update(['result' => 'Caries']);
        DB::table('patients')->where('result', 'Infection')->update(['result' => 'Gingivitis']);
        DB::table('scans')->where('ai_result', 'Cavity')->update(['ai_result' => 'Caries']);
        DB::table('scans')->where('ai_result', 'Infection')->update(['ai_result' => 'Gingivitis']);

        DB::statement("
            ALTER TABLE patients
            MODIFY result ENUM(
                'Healthy',
                'Calculus',
                'Caries',
                'Gingivitis',
                'Hypodontia',
                'Tooth Discoloration',
                'Ulcers'
            ) NULL
        ");

        DB::statement("
            ALTER TABLE scans
            MODIFY ai_result ENUM(
                'Healthy',
                'Calculus',
                'Caries',
                'Gingivitis',
                'Hypodontia',
                'Tooth Discoloration',
                'Ulcers'
            ) NOT NULL
        ");
    }
};
