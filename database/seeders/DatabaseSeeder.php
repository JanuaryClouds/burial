<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
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
            HandlerSeeder::class,
            WorkflowSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            InterviewSeeder::class,
            ClientAssessmentSeeder::class,
            ReferralSeeder::class,
            ClientRecommendationSeeder::class,
            BurialAssistanceSeeder::class,
            FuneralAssistanceSeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}
