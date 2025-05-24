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
        Schema::create('goal_combo_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_goal_id')->constrained('sales_goals')->onDelete('cascade');
            $table->string('combo_name');
            $table->text('combo_description')->nullable();
            $table->decimal('combo_price', 12, 2);
            $table->decimal('expected_profit', 12, 2);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_combo_recommendations');
    }
};
