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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->integer('groups_id')->unsigned();
            $table->integer('owner_id');
            $table->string('name', 500);
            $table->string('url_source', 500);
            $table->enum('type', ['photo', 'video']);
            $table->enum('network', ['vk', 'tumblr']);
            $table->boolean('is_parce')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
