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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // link to invoice
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();

            $table->string('name');                // lead guest name
            $table->unsignedInteger('number_of_pax');
            $table->date('arrival_date');          // required
            $table->date('departure_date')->nullable();
            $table->string('hotel_accommodation')->nullable();
            $table->string('tours')->nullable();   // you can copy tour_package here
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
