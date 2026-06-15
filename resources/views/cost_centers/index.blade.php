@extends('layouts.hr')

@section('title', 'مراكز التكلفة')
@section('page-title', 'مراكز التكلفة')

@section('content')
    <div class="page-hero">
        <div class="hero-info">
            <div class="hero-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <h1>مراكز التكلفة</h1>
                <p>إدارة مراكز التكلفة وربطها بالموظفين ومسير الرواتب.</p>
            </div>
        </div>

        @if(auth()->user()->hasPermission('cost_centers.create'))
            <div class="hero-actions">
                <a href="{{ route('cost-centers.create') }}" class="hero-btn white">
                    <i class="fas fa-plus"></i>
                    إضافة
                </a>
            </div>
        @endif
    </div>

    <div class="card" style="margin-bottom:18px">
        <form method="GET" action="{{ route('cost-centers.index') }}">
            <div style="display:grid;grid-template-columns:1fr 220px auto;gap:12px;align-items:end">
                <div>
                    <label>بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الكود">
                </div>

                <div>
                    <label>الحالة</label>
                    <select name="is_active">
                        <option value="">الكل</option>
                        <option value="1" @selected(request('is_active') === '1')>مفعل</option>
                        <option value="0" @selected(request('is_active') === '0')>غير مفعل</option>
                    </select>
                </div>

                <div style="display:flex;gap:8px">
                    <button class="btn">بحث</button>
                    <a href="{{ route('cost-centers.index') }}" class="btn btn-danger">مسح</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>الترتيب</th>
                <th>الاسم العربي</th>
                <th>الاسم الإنجليزي</th>
                <th>الكود</th>
                <th>الحالة</th>
                <th>عدد الموظفين</th>
                <th>الإجراءات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($costCenters as $item)
                <tr>
                    <td>{{ $item->sort_order }}</td>
                    <td>{{ $item->name_ar }}</td>
                    <td>{{ $item->name_en ?? '-' }}</td>
                    <td><strong>{{ $item->code }}</strong></td>
                    <td>
                        @if($item->is_active)
                            <span class="badge badge-active">مفعل</span>
                        @else
                            <span class="badge-danger-soft">غير مفعل</span>
                        @endif
                    </td>
                    <td>{{ $item->employees_count }}</td>
                    <td>
                        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                            @if(auth()->user()->hasPermission('cost_centers.edit'))
                                <a href="{{ route('cost-centers.edit', $item) }}" class="btn">تعديل</a>
                            @endif

                            @if(auth()->user()->hasPermission('cost_centers.delete'))
                                <form method="POST" action="{{ route('cost-centers.destroy', $item) }}" onsubmit="return confirm('هل تريد الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">حذف</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">لا توجد بيانات.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">
            {{ $costCenters->links() }}
        </div>
    </div>
@endsection
