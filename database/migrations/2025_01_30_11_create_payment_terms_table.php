<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g., "Net 30", "Due on Receipt"
            $table->integer('days');       // 0, 15, 30, 45, 60, etc.
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Modify invoices table to use payment terms
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('payment_terms_id')->after('issue_date')->constrained();
            $table->dropColumn('due_date'); // Remove the old due_date column
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->after('issue_date'); // Add back the due_date column
            $table->dropConstrainedForeignId('payment_terms_id');
        });

        Schema::dropIfExists('payment_terms');
    }
}; 