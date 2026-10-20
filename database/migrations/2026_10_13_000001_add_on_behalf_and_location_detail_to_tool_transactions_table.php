<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tool_transactions', function (Blueprint $table) {
            $table->string('on_behalf')->nullable();
            $table->string('location_detail')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tool_transactions', function (Blueprint $table) {
            $table->dropColumn(['on_behalf', 'location_detail']);
        });
    }
};
