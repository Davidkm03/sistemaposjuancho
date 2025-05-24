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
        Schema::create('sales_goals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2); // Monto objetivo
            $table->decimal('current_amount', 12, 2)->default(0.00); // Progreso actual
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'failed', 'cancelled'])->default('active');
            $table->boolean('deduct_expenses')->default(true); // Si descuenta gastos o no
            $table->json('recommendation_settings')->nullable(); // Configuración para recomendaciones
            $table->foreignId('user_id')->constrained(); // Usuario que creó la meta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_goals');
    }
};
