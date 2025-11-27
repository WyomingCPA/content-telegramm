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
        Schema::create('rss_items', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1500);
            $table->text('body');
            $table->string('link');
            $table->date('pubDate');
            $table->boolean('is_publish')->unsigned()->nullable();
            $table->timestamps();
        });

        // migrations/****_**_**_******_create_categories_table.php
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });

        // migrations/****_**_**_******_create_category_post_table.php
        Schema::create('category_post', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->unsignedBigInteger('post_id')->unsigned();
            $table->primary(['category_id', 'post_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rss_items');
    }
};
