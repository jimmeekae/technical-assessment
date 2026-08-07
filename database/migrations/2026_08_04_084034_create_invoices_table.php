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

            $table->string('invoice_number')->unique();
        
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->date('posting_date');
        
            $table->foreignId('sales_employee_id')
                ->constrained();
        
            $table->text('remarks');
        
            $table->decimal('total_before_discount',18,3)->default(0);
        
            $table->decimal('discount',18,3)->default(0);
        
            $table->decimal('total_after_discount',18,3)->default(0);
        
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        
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
