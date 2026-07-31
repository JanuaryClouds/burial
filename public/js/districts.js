export default function updateDistrict(barangay) {
    if ([
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
        ].includes(barangay)) {
        $('#district_id').val(1);
        $('#district_id_display').val(1);
    } else if ([
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
        ].includes(barangay)) {
        $('#district_id').val(2);
        $('#district_id_display').val(2);
    } else {
        $('#district_id').val("");
        $('#district_id_display').val("");
    }
}
