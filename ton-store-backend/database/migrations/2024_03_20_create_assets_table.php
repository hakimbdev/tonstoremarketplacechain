<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('image_url')->nullable();
            $table->decimal('price', 20, 9);
            $table->string('owner_address');
            $table->string('contract_address')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'sold', 'auction']);
            $table->timestamp('auction_end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}; 