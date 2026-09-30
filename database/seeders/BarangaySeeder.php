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
                'Bagong Tanyag',
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

    public function taguigBarangays(): array
    {
        return [
            "Tanyag",
            "Bagumbayan",
            "Bambang",
            "Calzada",
            "Hagonoy",
            "Ibayo-Tipas",
            "Ligid-Tipas",
            "Lower Bicutan",
            "Maharlika Village",
            "Napindan",
            "Palingon",
            "Santa Ana",
            "Central Signal Village",
            "Tuktukan",
            "Upper Bicutan",
            "Ususan",
            "Wawa",
            "Western Bicutan",
            "Central Bicutan",
            "Fort Bonifacio",
            "Katuparan",
            "New Lower Bicutan",
            "North Daang Hari",
            "North Signal Village",
            "Pinagsama",
            "San Miguel",
            "South Daang Hari",
            "South Signal Village",
            "Cembo",
            "Comembo",
            "East Rembo",
            "Pembo",
            "Pitogo",
            "Post Proper Northside",
            "Post Proper Southside",
            "Rizal",
            "South Cembo",
            "West Rembo"
        ];
    }

    public function paterosBarangays(): array
    {
        return [
            "Aguho",
            "Magtanggol",
            "Martires Del 96",
            "Poblacion",
            "San Pedro",
            "San Roque",
            "Santa Ana",
            "Santo Rosario-Kanluran",
            "Santo Rosario-Silangan",
            "Tabacalera"       
        ];
    }
}
