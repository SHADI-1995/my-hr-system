<?php

namespace App\Http\Controllers;

use App\Models\EmployeeHealthCard;
use App\Models\EmployeeIqama;
use App\Models\EmployeePassport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;


class DocumentTrackingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('documents.view'), 403);

        $document = $request->get('document', 'iqamas');
        $status = $request->get('status', 'near_expiry');

        $rows = $this->getFilteredDocuments($document, $status);

        $counts = [
            'iqamas' => [
                'valid' => $this->getFilteredDocuments('iqamas', 'valid')->count(),
                'near_expiry' => $this->getFilteredDocuments('iqamas', 'near_expiry')->count(),
                'expired' => $this->getFilteredDocuments('iqamas', 'expired')->count(),
            ],
            'passports' => [
                'valid' => $this->getFilteredDocuments('passports', 'valid')->count(),
                'near_expiry' => $this->getFilteredDocuments('passports', 'near_expiry')->count(),
                'expired' => $this->getFilteredDocuments('passports', 'expired')->count(),
            ],
            'health_cards' => [
                'valid' => $this->getFilteredDocuments('health_cards', 'valid')->count(),
                'near_expiry' => $this->getFilteredDocuments('health_cards', 'near_expiry')->count(),
                'expired' => $this->getFilteredDocuments('health_cards', 'expired')->count(),
            ],
        ];

        $lastUpdatedAt = Cache::get('documents_last_updated_at');

        return view('documents.index', compact(
            'rows',
            'counts',
            'document',
            'status',
            'lastUpdatedAt'
        ));

    }
    public function refresh(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('documents.view'), 403);

        Artisan::call('documents:update-remaining-days');

        return redirect()
            ->route('documents.index', [
                'document' => $request->get('document', 'iqamas'),
                'status' => $request->get('status', 'near_expiry'),
            ])
            ->with('success', 'تم تحديث بيانات الوثائق بنجاح');
    }
    public function export(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('documents.export'), 403);

        $document = $request->get('document', 'iqamas');
        $status = $request->get('status', 'near_expiry');

        $rows = $this->getFilteredDocuments($document, $status);

        $fileName = 'documents_' . $document . '_' . $status . '_' . now('Asia/Riyadh')->format('Y_m_d') . '.xls';

        return new StreamedResponse(function () use ($rows, $document, $status) {
            echo '<html>';
            echo '<head><meta charset="UTF-8"></head>';
            echo '<body dir="rtl">';

            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>نوع الوثيقة</th>';
            echo '<th>الحالة</th>';
            echo '<th>اسم الموظف</th>';
            echo '<th>رقم الوثيقة</th>';
            echo '<th>تاريخ البدء / الإصدار</th>';
            echo '<th>تاريخ الانتهاء</th>';
            echo '<th>الأيام المتبقية</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>' . e($this->documentTitle($document)) . '</td>';
                echo '<td>' . e($this->statusTitle($status)) . '</td>';
                echo '<td>' . e($row['employee_name']) . '</td>';
                echo '<td>' . e($row['document_number']) . '</td>';
                echo '<td>' . e($row['issue_date']) . '</td>';
                echo '<td>' . e($row['expiry_date']) . '</td>';
                echo '<td>' . e($row['remaining_days_text']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            echo '</body>';
            echo '</html>';
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function getFilteredDocuments(string $document, string $status)
    {
        return $this->getDocumentsQuery($document)
            ->where('document_status', $status)
            ->orderBy('remaining_days')
            ->get()
            ->map(function ($item) use ($document) {
                return [
                    'employee_name' => $item->employee->display_name ?? $item->employee->full_name ?? '-',
                    'document_number' => $this->documentNumber($item, $document),
                    'issue_date' => $item->issue_date ? Carbon::parse($item->issue_date)->format('Y-m-d') : '-',
                    'expiry_date' => $item->expiry_date ? Carbon::parse($item->expiry_date)->format('Y-m-d') : '-',
                    'remaining_days' => $item->remaining_days,
                    'remaining_days_text' => $this->remainingDaysText($item->remaining_days),
                    'status' => $item->document_status,
                ];
            });
    }

    private function getDocumentsQuery(string $document)
    {
        return match ($document) {
            'passports' => EmployeePassport::with('employee')->whereNotNull('expiry_date'),
            'health_cards' => EmployeeHealthCard::with('employee')->whereNotNull('expiry_date'),
            default => EmployeeIqama::with('employee')->whereNotNull('expiry_date'),
        };
    }

    private function documentNumber($item, string $document): string
    {
        return match ($document) {
            'passports' => $item->passport_number ?? '-',
            'health_cards' => $item->card_number ?? '-',
            default => $item->iqama_number ?? '-',
        };
    }

    private function remainingDaysText($remainingDays): string
    {
        if ($remainingDays === null) {
            return '-';
        }

        if ($remainingDays < 0) {
            return 'منتهي منذ ' . abs($remainingDays) . ' يوم';
        }

        return $remainingDays . ' يوم';
    }

    private function documentTitle(string $document): string
    {
        return match ($document) {
            'passports' => 'الجوازات',
            'health_cards' => 'الكروت الصحية',
            default => 'الإقامات',
        };
    }

    private function statusTitle(string $status): string
    {
        return match ($status) {
            'valid' => 'سارية',
            'near_expiry' => 'قريبة الانتهاء',
            'expired' => 'منتهية',
            default => $status,
        };
    }
}

