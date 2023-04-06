<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryProductTable extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventories');

            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->unique(['inventory_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_product');
    }
}
