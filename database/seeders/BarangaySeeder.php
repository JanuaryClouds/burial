<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        $barangays = [
            '1' => [
                'Pateros',
                'Bagumbayan',
                'Bambang',
                'Calzada',
                'Comembo',
                'Hagonoy',
                'Ibayo-tipas',
                'Ligid-tipas',
                'Lower bicutan',
                'New lower bicutan',
                'Napindan',
                'Palingon',
                'Pembo',
                'Rizal',
                'San miguel',
                'Sta Ana',
                'Tuktukan',
                'Ususan',
                'Wawa',
            ],
            '2' => [
                'Cembo',
                'Central bicutan',
                'Central signal village',
                'East rembo',
                'Fort bonifacio',
                'Katuparan',
                'Maharlika village',
                'North daang hari',
                'North signal village',
                'Pinagsama',
                'Pitogo',
                'Post proper northside',
                'Post proper southside',
                'South cembo',
                'South daang hari',
                'South signal village',
                'West rembo',
            ],
        ];

        foreach ($barangays as $key => $names) {
            foreach ($names as $name) {
                Barangay::firstOrCreate([
                    'district_id' => $key,
                    'name' => $name,
                    'remarks' => 'seeder generated',
                ]);
            }
        }
    }
}
