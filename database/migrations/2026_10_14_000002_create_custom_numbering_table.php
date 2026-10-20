<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_numbering', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->unsignedInteger('order')->default(0);
            $table->char('year', 2)->nullable();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_numbering');
    }
};
