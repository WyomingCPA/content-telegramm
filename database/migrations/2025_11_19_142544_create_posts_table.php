<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('owner_id');
            $table->string('text', 2000);
            $table->string('link');
            $table->json('attachments');
            $table->enum('type', ['photo', 'video', 'ad'])->default('photo');
            $table->enum('network', ['vk', 'tumblr', 'telegramm'])->default('vk');
            $table->boolean('is_publish')->unsigned()->nullable();
            $table->boolean('is_hidden')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
