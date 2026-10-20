<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tool_transaction_items', function (Blueprint $table) {
            $table->unsignedInteger('event_checklist_submission_id')->nullable()->after('asset_id');
            $table->foreign('event_checklist_submission_id', 'tool_transaction_items_checklist_submission_fk')
                ->references('id')
                ->on('event_checklist_submissions')
                ->nullOnDelete();
        });

        DB::table('tool_transaction_items')
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $transaction = DB::table('tool_transactions')
                        ->where('id', $item->transaction_id)
                        ->where('transaction_type', 'checkout')
                        ->first();

                    if (! $transaction) {
                        continue;
                    }

                    $submission = DB::table('event_checklist_submissions')
                        ->where('asset_id', $item->asset_id)
                        ->where('created_at', '>=', $transaction->created_at)
                        ->where('created_at', '<=', $item->created_at)
                        ->orderByDesc('created_at')
                        ->first();

                    if ($submission) {
                        DB::table('tool_transaction_items')
                            ->where('id', $item->id)
                            ->update(['event_checklist_submission_id' => $submission->id]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('tool_transaction_items', function (Blueprint $table) {
            $table->dropForeign('tool_transaction_items_checklist_submission_fk');
            $table->dropColumn('event_checklist_submission_id');
        });
    }
};
