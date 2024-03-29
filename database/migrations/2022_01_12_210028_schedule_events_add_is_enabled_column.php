<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduleEventsAddIsEnabledColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_events', function (Blueprint $table) {
            $table->boolean('is_enabled')
                ->after('last_error_message')
                ->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_events', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }
}
