@extends('layouts.hr')

@section('title', 'متابعة الوثائق')
@section('page-title', 'متابعة الوثائق')

@section('content')

    <style>
        .documents-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .document-box {
            background: #fff;
            border: 1px solid #eee7ff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.04);
        }

        .document-box h3 {
            margin: 0 0 15px 0;
            color: #4c3b91;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .status-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #eee;
            background: #fafafa;
            color: #333;
        }

        .status-btn.active {
            border-color: #6d5dfc;
            background: #f2edff;
            color: #4c3b91;
        }

        .status-count {
            background: #fff;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 13px;
            border: 1px solid #eee;
        }

        .status-valid {
            color: #166534;
        }

        .status-near {
            color: #92400e;
        }

        .status-expired {
            color: #991b1b;
        }

        .tracking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .badge-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-valid {
            background: #dcfce7;
            color: #166534;
        }

        .badge-near {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 1000px) {
            .documents-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
       $documentTitles = [
                'iqamas' => 'الإقامات',
                'passports' => 'الجوازات',
                'health_cards' => 'الكروت الصحية',
            ];

            $statusTitles = [
                'valid' => 'سارية',
                'near_expiry' => 'قريبة الانتهاء',
                'expired' => 'منتهية',
            ];

            $statusClasses = [
                'valid' => 'badge-valid',
                'near_expiry' => 'badge-near',
                'expired' => 'badge-expired',
            ];
    @endphp
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon">
                <i class="fas fa-file-shield"></i>
            </div>

            <div>
                <h1>متابعة الوثائق</h1>
                <p>
                    متابعة الإقامات والجوازات والكروت الصحية حسب تاريخ الانتهاء
                    <br>
                    <small style="opacity:.85;">
                        آخر تحديث للبيانات:
                        {{ $lastUpdatedAt ?? 'لم يتم التحديث بعد' }}
                        بتوقيت السعودية
                    </small>
                </p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('documents.refresh', ['document' => $document, 'status' => $status]) }}" class="hero-btn white">
                <i class="fas fa-rotate-right"></i>
                تحديث البيانات
            </a>
        </div>
    </div>

    <div class="documents-summary">

        <div class="document-box">
            <h3>
                <i class="fas fa-id-card"></i>
                الإقامات
            </h3>

            <div class="status-buttons">
                <a class="status-btn {{ $document == 'iqamas' && $status == 'valid' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'valid']) }}">
                    <span class="status-valid">سارية</span>
                    <span class="status-count">{{ $counts['iqamas']['valid'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'iqamas' && $status == 'near_expiry' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'near_expiry']) }}">
                    <span class="status-near">قريبة الانتهاء</span>
                    <span class="status-count">{{ $counts['iqamas']['near_expiry'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'iqamas' && $status == 'expired' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'iqamas', 'status' => 'expired']) }}">
                    <span class="status-expired">منتهية</span>
                    <span class="status-count">{{ $counts['iqamas']['expired'] }}</span>
                </a>
            </div>
        </div>

        <div class="document-box">
            <h3>
                <i class="fas fa-passport"></i>
                الجوازات
            </h3>

            <div class="status-buttons">
                <a class="status-btn {{ $document == 'passports' && $status == 'valid' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'passports', 'status' => 'valid']) }}">
                    <span class="status-valid">سارية</span>
                    <span class="status-count">{{ $counts['passports']['valid'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'passports' && $status == 'near_expiry' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'passports', 'status' => 'near_expiry']) }}">
                    <span class="status-near">قريبة الانتهاء</span>
                    <span class="status-count">{{ $counts['passports']['near_expiry'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'passports' && $status == 'expired' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'passports', 'status' => 'expired']) }}">
                    <span class="status-expired">منتهية</span>
                    <span class="status-count">{{ $counts['passports']['expired'] }}</span>
                </a>
            </div>
        </div>

        <div class="document-box">
            <h3>
                <i class="fas fa-notes-medical"></i>
                الكروت الصحية
            </h3>

            <div class="status-buttons">
                <a class="status-btn {{ $document == 'health_cards' && $status == 'valid' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'valid']) }}">
                    <span class="status-valid">سارية</span>
                    <span class="status-count">{{ $counts['health_cards']['valid'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'health_cards' && $status == 'near_expiry' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'near_expiry']) }}">
                    <span class="status-near">قريبة الانتهاء</span>
                    <span class="status-count">{{ $counts['health_cards']['near_expiry'] }}</span>
                </a>

                <a class="status-btn {{ $document == 'health_cards' && $status == 'expired' ? 'active' : '' }}"
                   href="{{ route('documents.index', ['document' => 'health_cards', 'status' => 'expired']) }}">
                    <span class="status-expired">منتهية</span>
                    <span class="status-count">{{ $counts['health_cards']['expired'] }}</span>
                </a>
            </div>
        </div>

    </div>

    <div class="card">

        <div class="tracking-header">
            <div>
                <h3 style="color:#4c3b91;margin:0;">
                    {{ $documentTitles[$document] ?? $document }}
                    —
                    {{ $statusTitles[$status] ?? $status }}
                </h3>
                <p style="color:#6b7280;margin:6px 0 0;">
                    عدد السجلات: {{ $rows->count() }}
                </p>
            </div>

            @if(auth()->user()->hasPermission('documents.export'))
                <a href="{{ route('documents.export', ['document' => $document, 'status' => $status]) }}" class="btn">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            @endif
        </div>

        <table>
            <thead>
            <tr>
                <th>اسم الموظف</th>
                <th>رقم الوثيقة</th>
                <th>تاريخ البدء / الإصدار</th>
                <th>تاريخ الانتهاء</th>
                <th>الأيام المتبقية</th>
                <th>الحالة</th>
            </tr>
            </thead>

            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['employee_name'] }}</td>
                    <td>{{ $row['document_number'] }}</td>
                    <td>{{ $row['issue_date'] }}</td>
                    <td>{{ $row['expiry_date'] }}</td>
                    <td>
                        @if($row['remaining_days'] < 0)
                            منتهي منذ {{ abs($row['remaining_days']) }} يوم
                        @else
                            {{ $row['remaining_days'] }} يوم
                        @endif
                    </td>
                    <td>
                    <span class="badge-status {{ $statusClasses[$row['status']] ?? '' }}">
                        {{ $statusTitles[$row['status']] ?? $row['status'] }}
                    </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">لا توجد بيانات لهذا الفلتر</td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

@endsection
