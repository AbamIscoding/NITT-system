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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('lead_guest_name');
            $table->string('email');

            $table->unsignedInteger('number_of_pax')->default(1);
            $table->string('hotel_accommodation')->nullable();
            $table->string('tour_package')->nullable();

            $table->decimal('rate_per_pax', 10, 2)->default(0); //auto calculated formula: number_of_pax * rate_per_pax
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('downpayment', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->default(0); //auto calculated formula: total_amount - downpayment

            $table->string('status')->default('pending');

            $table->date('arrival_date');
            $table->date('departure_date')->nullable();
            $table->date('due_date');
            $table->date('date_issued'); //auto set to current date on creation

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
