<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('user_id')->constrained()->onDelete('cascade'); // Related to the user
            $table->string('description');
            $table->decimal('amount', 10, 2); // Amount of the transaction
            $table->date('date');
            $table->enum('type', ['income', 'expense']); // Define whether it is an income or expense
            $table->string('category')->nullable(); // Optional category
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
