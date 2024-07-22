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
            $table->id();
            $table->string('name'); // Nom du produit
            $table->text('description')->nullable(); // Description du produit, nullable pour permettre des valeurs nulles
            $table->decimal('price', 8, 2); // Prix du produit avec 8 chiffres au total et 2 chiffres après la virgule
            $table->json('size')->nullable(); // Ensemble des tailles disponibles, stocké sous forme de JSON
            $table->json('color')->nullable(); // Couleur du produit, nullable
            $table->integer('quantity')->default(0); // Quantité du produit, valeur par défaut 0
            $table->json('imageUrl')->nullable(); // URL des images du produit, stocké sous forme de JSON
            $table->boolean('stock')->default(true); // Disponibilité du produit, valeur par défaut true (disponible)
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade')->onUpdate('cascade');
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
