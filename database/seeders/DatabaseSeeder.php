<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // System
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            AdminSeeder::class,
            SystemSettingSeeder::class,

            // Reference
            AssistanceSeeder::class,
            MoaSeeder::class,
            CivilSeeder::class,
            // DistrictSeeder::class,
            EducationSeeder::class,
            NationalitySeeder::class,
            RelationshipSeeder::class,
            ReligionSeeder::class,
            SexSeeder::class,
            // BarangaySeeder::class,
            // Workflow
            WorkflowSeeder::class,
            WorkflowStageSeeder::class,

            FuneralAssistanceTypeSeeder::class,
            DocumentRequirementSeeder::class,

            // Data
            ClientSeeder::class,
            BeneficiarySeeder::class,
            ApplicationSeeder::class,
            InterviewSeeder::class,
            AssessmentSeeder::class,
            RecommendationSeeder::class,
            WorkflowHistorySeeder::class,
            ReferralSeeder::class,

            // Custom Fields
        ]);
    }
}
