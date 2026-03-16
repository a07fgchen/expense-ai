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
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('使用者ID');
            $table->decimal('amount', 10, 2)->comment('消費金額');
            $table->string('category')->index()->comment('消費列別');
            $table->string('description')->comment('消費描述');
            $table->text('raw_text')->nullable()->comment('原始文字');
            $table->decimal('ai_confidence', 3, 2)->nullable()->comment('AI信心度');
            $table->string('ai_model')->nullable()->comment('AI模型');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
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
