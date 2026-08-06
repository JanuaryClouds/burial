<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // System
            UserSeeder::class,
            AdminSeeder::class,
            RolePermissionSeeder::class,
            SystemSettingSeeder::class,

            // Reference
            AssistanceSeeder::class,
            MoaSeeder::class,
            CivilSeeder::class,
            DistrictSeeder::class,
            EducationSeeder::class,
            NationalitySeeder::class,
            RelationshipSeeder::class,
            ReligionSeeder::class,
            SexSeeder::class,
            BarangaySeeder::class,
            FuneralAssistanceTypeSeeder::class,

            // Data
            ClientSeeder::class,
            BeneficiarySeeder::class,
            ApplicationSeeder::class,
            InterviewSeeder::class,
            AssessmentSeeder::class,
            RecommendationSeeder::class,
            ReferralSeeder::class,

            // Custom Fields
        ]);
    }
}
