<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->enum('type', ['apartment', 'house', 'studio', 'condominium', 'commercial', 'office']);
            $table->decimal('price', 12, 2);
            $table->string('currency')->default('ETB');
            $table->string('city');
            $table->string('sub_city')->nullable();
            $table->string('address')->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->unsignedTinyInteger('kitchens')->default(0);
            $table->enum('furnishing_status', ['furnished', 'semi-furnished', 'unfurnished'])->default('unfurnished');
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->date('available_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'rented', 'unavailable'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('owner_id');
            $table->index('city');
            $table->index('status');
            $table->index('type');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
