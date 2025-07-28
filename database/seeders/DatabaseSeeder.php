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
            BaoAccountSeeder::class,
            NonRelativeRelationships::class,
            SampleBurialServiceProviders::class,
            SpouseRelationship::class,
        ]);
    }
}