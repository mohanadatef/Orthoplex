<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('webhook_dlq', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id')->nullable();
            $table->string('url');
            $table->string('event');
            $table->json('payload');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('magic_links');
    }
};
