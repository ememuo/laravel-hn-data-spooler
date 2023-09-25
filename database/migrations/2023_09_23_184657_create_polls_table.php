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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('by');
            $table->string('type');
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->integer('score');
            $table->json('kids')->nullable();
            $table->json('parts')->nullable();
            $table->timestamp('time');
            $table->integer('descendants')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
