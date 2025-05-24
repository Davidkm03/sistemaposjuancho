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
        Schema::table('accounting_transactions', function (Blueprint $table) {
            // Añadir campo para notas
            if (!Schema::hasColumn('accounting_transactions', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_transactions', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
