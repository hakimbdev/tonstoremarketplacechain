<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('transaction_hash');
            $table->string('from_address');
            $table->string('to_address');
            $table->decimal('amount', 20, 9);
            $table->enum('type', ['purchase', 'bid', 'auction_end']);
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}; 