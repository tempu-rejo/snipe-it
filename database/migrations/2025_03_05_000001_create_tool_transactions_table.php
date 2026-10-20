<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tool_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('transaction_type', ['checkin', 'checkout', 'maintenance']);
            $table->string('document_number', 20)->unique();
            $table->integer('user_id')->unsigned();
            $table->longText('notes')->nullable();
            $table->date('transaction_date');
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['transaction_type', 'document_number']);
            $table->index(['user_id']);
            $table->index(['created_by']);
            $table->index(['transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tool_transactions');
    }
}
