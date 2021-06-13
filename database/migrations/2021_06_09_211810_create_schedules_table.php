<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_available')->default(true);
            $table->foreignId('bot_id');
            $table->foreignId('chat_id');
            $table->foreignId('message_id');
            $table->string('cron_expression');
            $table->json('settings')->nullable();
            $table->dateTimeTz('next_due_date');
            $table->text('last_error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_events');
    }
}
