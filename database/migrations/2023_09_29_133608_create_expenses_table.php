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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
             $table->string('expense_description');
             $table->string('expense_amount');
             $table->string('expense_status');
             $table->string('expense_date');
             $table->string('expense_month');
             $table->string('expense_year');
            $table->foreignId('category_id')
            ->constrained('categories')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('user_id')
            ->constrained('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
