<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sequences = [];

        $existingNumbers = DB::table('asset_maintenances')
            ->whereNotNull('document_number')
            ->pluck('document_number');

        foreach ($existingNumbers as $documentNumber) {
            if (preg_match('/^(MNT-\d{4})-(\d+)$/', $documentNumber, $matches)) {
                $sequences[$matches[1]] = max($sequences[$matches[1]] ?? 0, (int) $matches[2]);
            }
        }

        DB::table('asset_maintenances')
            ->where(function ($query): void {
                $query->whereNull('document_number')
                    ->orWhere('document_number', '');
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->select(['id', 'created_at'])
            ->chunkById(100, function ($maintenances) use (&$sequences): void {
                foreach ($maintenances as $maintenance) {
                    $createdAt = $maintenance->created_at ? new DateTime($maintenance->created_at) : new DateTime();
                    $prefix = 'MNT-'.$createdAt->format('ym');
                    $sequences[$prefix] = ($sequences[$prefix] ?? 0) + 1;

                    DB::table('asset_maintenances')
                        ->where('id', $maintenance->id)
                        ->update([
                            'document_number' => $prefix.'-'.str_pad((string) $sequences[$prefix], 5, '0', STR_PAD_LEFT),
                        ]);
                }
            });
    }

    public function down(): void
    {
    }
};
