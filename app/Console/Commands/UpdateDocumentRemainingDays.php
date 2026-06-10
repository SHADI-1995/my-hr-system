<?php

namespace App\Console\Commands;

use App\Models\EmployeeHealthCard;
use App\Models\EmployeeIqama;
use App\Models\EmployeePassport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
class UpdateDocumentRemainingDays extends Command
{
    protected $signature = 'documents:update-remaining-days';

    protected $description = 'Update remaining days and document status for employee documents';

    public function handle(): int
    {
        $today = Carbon::now('Asia/Riyadh')->startOfDay();

        $this->updateIqamas($today);
        $this->updatePassports($today);
        $this->updateHealthCards($today);
        Cache::forever('documents_last_updated_at', Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s'));
        $this->info('تم تحديث الأيام المتبقية وحالة الوثائق بنجاح');
        $this->info('وقت التحديث: ' . Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    private function updateIqamas(Carbon $today): void
    {
        EmployeeIqama::whereNotNull('expiry_date')
            ->chunkById(100, function ($items) use ($today) {
                foreach ($items as $item) {
                    $remainingDays = $today->diffInDays(
                        Carbon::parse($item->expiry_date)->startOfDay(),
                        false
                    );

                    $item->forceFill([
                        'remaining_days' => $remainingDays,
                        'document_status' => $this->calculateStatus($remainingDays),
                    ])->save();
                }
            });
    }

    private function updatePassports(Carbon $today): void
    {
        EmployeePassport::whereNotNull('expiry_date')
            ->chunkById(100, function ($items) use ($today) {
                foreach ($items as $item) {
                    $remainingDays = $today->diffInDays(
                        Carbon::parse($item->expiry_date)->startOfDay(),
                        false
                    );

                    $item->forceFill([
                        'remaining_days' => $remainingDays,
                        'document_status' => $this->calculateStatus($remainingDays),
                    ])->save();
                }
            });
    }

    private function updateHealthCards(Carbon $today): void
    {
        EmployeeHealthCard::whereNotNull('expiry_date')
            ->chunkById(100, function ($items) use ($today) {
                foreach ($items as $item) {
                    $remainingDays = $today->diffInDays(
                        Carbon::parse($item->expiry_date)->startOfDay(),
                        false
                    );

                    $item->forceFill([
                        'remaining_days' => $remainingDays,
                        'document_status' => $this->calculateStatus($remainingDays),
                    ])->save();
                }
            });
    }

    private function calculateStatus(int $remainingDays): string
    {
        if ($remainingDays < 0) {
            return 'expired';
        }

        if ($remainingDays <= 30) {
            return 'near_expiry';
        }

        return 'valid';
    }
}
