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
        Schema::table('invoices', function (Blueprint $table) {
             $table->unsignedInteger('adult_count')->default(0);
            $table->decimal('adult_rate', 10, 2)->default(0);

            $table->unsignedInteger('infant_count')->default(0);
            $table->decimal('infant_rate', 10, 2)->default(0);

            $table->unsignedInteger('senior_count')->default(0);
            $table->decimal('senior_rate', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
             $table->dropColumn([
            'adult_count', 'adult_rate',
            'infant_count', 'infant_rate',
            'senior_count', 'senior_rate',
            ]);
        });
    }
};
