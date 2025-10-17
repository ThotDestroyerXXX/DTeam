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
        Schema::create('games', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->text('trailer_url')->nullable();
            $table->text('brief_description');
            $table->text('full_description');
            $table->date('release_date');
            $table->decimal('price', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->foreignId('age_rating_id')->constrained('age_ratings')->onDelete('cascade');
            $table->foreignUlid('publisher_id')->constrained('publishers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
