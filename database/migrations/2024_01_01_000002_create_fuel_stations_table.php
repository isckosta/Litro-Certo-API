<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fuel_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('cnpj', 18)->unique();
            $table->string('address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 9);
            $table->string('phone', 20)->nullable();
            $table->geography('location', 'point', 4326)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('services')->nullable(); // ["wifi", "convenience_store", "car_wash"]
            $table->json('payment_methods')->nullable(); // ["credit_card", "debit_card", "pix"]
            $table->json('opening_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Regular indexes
            $table->index(['city', 'state']);
            $table->index('is_active');
            $table->index('brand');
        });

        // Add spatial index after table creation (nullable columns can't have spatial index in creation)
        DB::statement('CREATE INDEX fuel_stations_location_idx ON fuel_stations USING GIST (location) WHERE location IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stations');
    }
};
