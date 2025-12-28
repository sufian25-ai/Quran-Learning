<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->unique(); // Internal transaction ID
            $table->string('gateway'); // stripe, sslcommerz, paypal, bkash, etc.
            $table->string('gateway_transaction_id')->nullable(); // External gateway ID
            $table->decimal('amount', 10, 2);
            $table->decimal('gateway_fee', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->enum('type', ['enrollment', 'renewal', 'upgrade', 'addon', 'refund'])->default('enrollment');
            $table->json('gateway_response')->nullable(); // Full response from payment gateway
            $table->json('metadata')->nullable(); // Additional payment details
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->text('refund_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_url')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('gateway');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
