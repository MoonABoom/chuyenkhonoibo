<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('inventory_transfers', function (Blueprint $table) {
        $table->unsignedBigInteger('from_warehouse_id')->nullable();
        $table->unsignedBigInteger('to_warehouse_id')->nullable();
    });
}

public function down()
{
    Schema::table('inventory_transfers', function (Blueprint $table) {
        $table->dropColumn(['from_warehouse_id', 'to_warehouse_id']);
    });
}
};
