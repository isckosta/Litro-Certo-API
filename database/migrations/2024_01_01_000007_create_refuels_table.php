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
        Schema::create('refuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('fuel_station_id')->constrained()->onDelete('cascade');
            $table->enum('fuel_type', ['gasoline', 'ethanol', 'diesel', 'diesel_s10', 'gnv']);
            $table->decimal('liters', 8, 3);
            $table->decimal('price_per_liter', 8, 3);
            $table->decimal('total_amount', 10, 2);
            $table->integer('odometer')->nullable();
            $table->boolean('full_tank')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('refueled_at');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'refueled_at']);
            $table->index('fuel_station_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refuels');
    }
};
