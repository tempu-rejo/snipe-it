<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_checklist_submissions', function (Blueprint $table) {
            $table->json('evidence_file')->nullable()->after('rows');
        });
    }

    public function down(): void
    {
        Schema::table('event_checklist_submissions', function (Blueprint $table) {
            $table->dropColumn('evidence_file');
        });
    }
};
