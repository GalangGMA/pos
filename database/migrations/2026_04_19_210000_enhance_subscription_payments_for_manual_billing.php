<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('id');
            $table->timestamp('due_at')->nullable()->after('paid_at');
            $table->text('notes')->nullable()->after('reference_no');
            $table->string('proof_of_payment')->nullable()->after('notes');
            $table->foreignId('reviewed_by')->nullable()->after('proof_of_payment')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');

            $table->unique('invoice_no');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropUnique(['invoice_no']);
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'invoice_no',
                'due_at',
                'notes',
                'proof_of_payment',
                'reviewed_at',
                'rejection_reason',
            ]);
        });
    }
};
