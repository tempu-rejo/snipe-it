<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_checklist_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->json('columns');
            $table->json('rows')->nullable();
            $table->timestamps();
        });

        Schema::table('event_checklists', function (Blueprint $table) {
            $table->unsignedInteger('template_id')->nullable()->after('to_status_id');
            $table->foreign('template_id')
                ->references('id')
                ->on('event_checklist_templates')
                ->restrictOnDelete();
        });

        foreach (DB::table('event_checklists')->orderBy('id')->get() as $checklist) {
            $columns = json_decode($checklist->columns ?? '[]', true);
            $rows = json_decode($checklist->rows ?? '[]', true);

            $templateId = DB::table('event_checklist_templates')->insertGetId([
                'name' => $checklist->name,
                'columns' => json_encode(is_array($columns) ? $columns : []),
                'rows' => json_encode(is_array($rows) ? $rows : []),
                'created_at' => $checklist->created_at ?? now(),
                'updated_at' => $checklist->updated_at ?? now(),
            ]);

            DB::table('event_checklists')
                ->where('id', $checklist->id)
                ->update(['template_id' => $templateId]);
        }

        Schema::table('event_checklists', function (Blueprint $table) {
            $table->dropColumn(['columns', 'rows']);
        });
    }

    public function down(): void
    {
        Schema::table('event_checklists', function (Blueprint $table) {
            $table->json('columns')->nullable();
            $table->json('rows')->nullable();
        });

        foreach (
            DB::table('event_checklists')
                ->leftJoin('event_checklist_templates', 'event_checklists.template_id', '=', 'event_checklist_templates.id')
                ->select('event_checklists.id', 'event_checklist_templates.columns', 'event_checklist_templates.rows')
                ->orderBy('event_checklists.id')
                ->get() as $checklist
        ) {
            DB::table('event_checklists')
                ->where('id', $checklist->id)
                ->update([
                    'columns' => $checklist->columns ?? '[]',
                    'rows' => $checklist->rows ?? '[]',
                ]);
        }

        Schema::table('event_checklists', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });

        Schema::dropIfExists('event_checklist_templates');
    }
};
