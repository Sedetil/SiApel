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
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->string('card_uid', 64)->index()->comment('UID kartu RFID');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('deactivated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index gabungan untuk pencarian cepat kartu aktif
            $table->index(['card_uid', 'is_active']);
            $table->index(['participant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
