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
        Schema::create('discard_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discard_transfer_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discard_transfer_items');
    }
};
