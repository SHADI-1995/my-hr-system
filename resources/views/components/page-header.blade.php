<div class="page-hero">
    <div class="hero-info">
        <div class="hero-icon">
            <i class="{{ $icon ?? 'fas fa-table' }}"></i>
        </div>
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle ?? '' }}</p>
        </div>
    </div>

    <div class="hero-actions">
        <button onclick="exportTableToExcel()" class="hero-btn">
            <i class="fas fa-file-excel"></i> تصدير إكسل
        </button>

        <button onclick="exportTableToWord()" class="hero-btn">
            <i class="fas fa-file-word"></i> تصدير وورد
        </button>

        <a href="{{ $createRoute }}" class="hero-btn white">
            <i class="fas fa-plus"></i> إضافة جديد
        </a>
    </div>
</div>
