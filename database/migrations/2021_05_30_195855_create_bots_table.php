<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token');
            $table->string('real_first_name')->nullable();
            $table->string('real_user_name')->nullable();
            $table->string('real_id')->nullable();
            $table->boolean('is_available')->default(false);
            $table->text('last_error_message')->nullable();
            $table->dateTime('last_check_at')->nullable();
            $table->foreignId('user_id')->index();;
            $table->foreignId('team_id')->index();;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bots');
    }
}
