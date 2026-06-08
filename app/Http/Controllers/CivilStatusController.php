<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\CivilStatusRequest;
use App\Models\CivilStatus;
use App\Services\CivilStatusService;
use Illuminate\Support\Facades\Auth;

class CivilStatusController extends Controller
{
    protected $civilStatusServices;

    public function __construct(CivilStatusService $civilStatusServices)
    {
        $this->civilStatusServices = $civilStatusServices;
    }
}
