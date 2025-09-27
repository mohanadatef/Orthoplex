<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('endpoint');
            $table->string('method', 8);
            $table->string('request_hash', 64)->nullable();
            $table->json('response_body')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('idempotency_keys'); }
};
