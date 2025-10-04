<?php

use App\Enums\GameGiftStatus;
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
        Schema::create('game_gifts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignUlid('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignUlid('receiver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', array_column(GameGiftStatus::cases(), 'value'))->default(GameGiftStatus::PENDING->value);
            $table->text('message');
            $table->integer('discount_percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_gifts');
    }
};
