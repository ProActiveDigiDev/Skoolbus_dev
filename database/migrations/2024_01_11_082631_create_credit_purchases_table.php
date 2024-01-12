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
        Schema::create('credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('credits_purchased')->nullable()->default(0);
            $table->integer('cost_per_credit_at_purchase')->nullable()->default(0);
            $table->integer('total_amount')->nullable()->default(0)->comment('credits_purchased * cost_per_credit_at_purchase (Payfast amount_gross)');
            $table->integer('amount_fee')->nullable()->default(0); //payfast fees deducted
            $table->integer('amount_net')->nullable()->default(0); //payfast the net amount
            $table->string('m_payment_id')->nullable();  
            $table->string('pf_payment_id')->nullable();
            $table->string('payment_status')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_purchases');
    }
};
