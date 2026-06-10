<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeIqama;
use App\Models\EmployeePassport;
use App\Models\EmployeeHealthCard;

class DashboardController extends Controller
{
    public function index()
    {
        $employeesCount = class_exists(Employee::class) ? Employee::count() : 0;
        $departmentsCount = class_exists(Department::class) ? Department::count() : 0;
        $positionsCount = class_exists(Position::class) ? Position::count() : 0;

        $documentsStats = [
            'iqamas_near_expiry' => EmployeeIqama::where('document_status', 'near_expiry')->count(),
            'passports_near_expiry' => EmployeePassport::where('document_status', 'near_expiry')->count(),
            'health_cards_near_expiry' => EmployeeHealthCard::where('document_status', 'near_expiry')->count(),

            'iqamas_expired' => EmployeeIqama::where('document_status', 'expired')->count(),
            'passports_expired' => EmployeePassport::where('document_status', 'expired')->count(),
            'health_cards_expired' => EmployeeHealthCard::where('document_status', 'expired')->count(),
        ];

        $documentsStats['total_near_expiry'] =
            $documentsStats['iqamas_near_expiry'] +
            $documentsStats['passports_near_expiry'] +
            $documentsStats['health_cards_near_expiry'];

        $documentsStats['total_expired'] =
            $documentsStats['iqamas_expired'] +
            $documentsStats['passports_expired'] +
            $documentsStats['health_cards_expired'];

        return view('dashboard', compact(
            'employeesCount',
            'departmentsCount',
            'positionsCount',
            'documentsStats'
        ));
    }
}
