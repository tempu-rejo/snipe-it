<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('from_status_id')->nullable();
            $table->unsignedInteger('to_status_id');
            $table->string('name');
            $table->json('columns');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'from_status_id', 'to_status_id']);
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('from_status_id')->references('id')->on('status_labels');
            $table->foreign('to_status_id')->references('id')->on('status_labels');
        });

        Schema::create('event_checklist_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_checklist_id');
            $table->unsignedInteger('asset_id');
            $table->unsignedInteger('from_status_id')->nullable();
            $table->unsignedInteger('to_status_id');
            $table->json('rows');
            $table->unsignedInteger('submitted_by')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'created_at']);
            $table->foreign('event_checklist_id')->references('id')->on('event_checklists')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->foreign('from_status_id')->references('id')->on('status_labels');
            $table->foreign('to_status_id')->references('id')->on('status_labels');
            $table->foreign('submitted_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_checklist_submissions');
        Schema::dropIfExists('event_checklists');
    }
};
