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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('from_asset_id');
            $table->unsignedBigInteger('from_network_id');
            $table->string('from_address');
            $table->unsignedBigInteger('to_asset_id');
            $table->unsignedBigInteger('to_network_id');
            $table->string('to_address');
            $table->decimal('coin_amount',18,6);
            $table->decimal('dollar_amount',22,10)->nullable();
            $table->decimal('fee_coin',18,6)->nullable();
            $table->decimal('fee_dollar',22,10)->nullable();
            $table->decimal('to_amount',18,6)->nullable();
            $table->decimal('to_dollar',22,10)->nullable();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_asset_id')->references('id')->on('assets');
            $table->foreign('from_network_id')->references('id')->on('networks');
            $table->foreign('to_asset_id')->references('id')->on('assets');
            $table->foreign('to_network_id')->references('id')->on('assets');
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
