<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gunakan dropIfExists untuk memastikan tabel dihapus jika sudah ada
        Schema::dropIfExists('transactions');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('customer_id')->nullable()->index();
            $table->foreign('customer_id')->references('id')->on('members')->onDelete('set null');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->integer('points_used')->default(0);
            $table->integer('points_earned')->default(0);
            $table->timestamps();

            // Tambahkan index tambahan jika diperlukan
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
