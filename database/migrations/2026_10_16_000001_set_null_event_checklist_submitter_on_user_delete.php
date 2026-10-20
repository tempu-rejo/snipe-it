<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_checklist_submissions', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->foreign('submitted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_checklist_submissions', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->foreign('submitted_by')
                ->references('id')
                ->on('users');
        });
    }
};
