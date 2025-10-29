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
        Schema::create('products', function (Blueprint $table) {
                        // Basic Info
            $table->string('name'); // Medicine name
            $table->text('description')->nullable(); // Details

            // Inventory Info
            $table->integer('quantity')->default(0); // Stock count
            $table->decimal('price', 10, 2); // Price per unit
            

            // Relation with Category
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade'); // delete products if category deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
