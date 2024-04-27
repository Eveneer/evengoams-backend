<?php

use App\Enums\TransactionTypesEnum;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->unsignedBigInteger('amount');
            $table->uuid('author_id')->references('id')->on('users');
            $table->enum('type', TransactionTypesEnum::getValues());
            $table->string('concernable_type');
            $table->uuid('concernable_id');
            $table->uuid('parent_id')->nullable()->references('id')->on('transactions');
            $table->string('note')->nullable();
            $table->json('tag_ids')->nullable();
            $table->boolean('is_last')->default(true);
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
