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
        Schema::create('goal_product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_goal_id')->constrained('sales_goals')->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('recommended_quantity');
            $table->decimal('expected_revenue', 12, 2);
            $table->integer('priority')->default(0); // Para ordenar recomendaciones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_product_recommendations');
    }
};
