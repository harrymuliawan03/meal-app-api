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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('image')->nullable();
            $table->text('ingredients');
            $table->text('steps');
            $table->integer('duration');
            $table->enum('complexity', ['simple', 'medium', 'complex']);
            $table->enum('affordability', ['affordable', 'pricey', 'luxurious']);
            $table->boolean('isGlutenFree');
            $table->boolean('isLactoseFree');
            $table->boolean('isVegan');
            $table->boolean('isVegetarian');
            $table->timestamps();
        });

        Schema::create('category_meal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_meal');
        Schema::dropIfExists('meals');
    }
};
