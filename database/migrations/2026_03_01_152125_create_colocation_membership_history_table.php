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
        Schema::create('colocation_membership_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('colocation_id')->constrained()->onDelete('cascade');
            $table->enum('colocation_role', ['owner', 'member']);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->text('leave_reason')->nullable();
            $table->decimal('debt_amount', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'colocation_id']);
            $table->index(['user_id', 'left_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colocation_membership_history');
    }
};
