<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "emp_id",
        "firstname",
        "middlename",
        "lastname",
        "suffix",
        "gender",
        "contact_number",
        "birthday",
        "age",
        "username",
        "email",
        "civil_status",
        "img_path",
        "otp",
        "is_active",
        "access_level",
        "user_role",
        "created_at",
        "updated_at",
        "activated_at",
        "reset_password_otp",
        "position",
        "latest_address",
        "address_line_1",
        "street",
        "barangay",
        "city",
        "province",
        "zipcode",
        "full_address",
    ];

    public static function records()
    {
        if (!session('citizen')) {
            return false;
        }

        return Client::where('citizen_id', session('citizen')['user_id'])
            ->with(['interviews', 'claimant', 'funeralAssistance'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
