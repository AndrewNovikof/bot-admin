<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->index();
            $table->string('name');
            $table->text('text');
            $table->enum('parse_mode', ['Markdown', 'MarkdownV2', 'HTML'])->default('HTML');
            $table->boolean('disable_web_page_preview')->default(false);
            $table->boolean('disable_notification')->default(false);
            $table->string('reply_to_message_id')->nullable();
            $table->json('reply_markup')->nullable();
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
        Schema::dropIfExists('messages');
    }
}
