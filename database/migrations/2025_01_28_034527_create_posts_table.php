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

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->unique();
            $table->timestamps();
        });


        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("thumbnail")->nullable();
            $table->string("title");
            $table->string("content")->nullable();
            $table->string("color");
            $table->string("slug")->unique();
            $table->foreignId("category_id")->nullable()->constrained("categories")->cascadeOnUpdate()->nullOnDelete();
            $table->string("tags")->nullable();
            $table->boolean("published")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('posts');
    }
};
