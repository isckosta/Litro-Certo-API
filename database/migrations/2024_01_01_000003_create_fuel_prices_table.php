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
        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_station_id')->constrained()->onDelete('cascade');
            $table->enum('fuel_type', ['gasoline', 'ethanol', 'diesel', 'diesel_s10', 'gnv']);
            $table->decimal('price', 8, 3);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_current')->default(true);
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Composite indexes for efficient queries
            $table->index(['fuel_station_id', 'fuel_type', 'is_current']);
            $table->index(['fuel_type', 'price']);
            $table->index('valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_prices');
    }
};
