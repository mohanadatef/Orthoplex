<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gdpr_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token', 64)->unique();
            $table->string('path');
            $table->timestamp('available_until')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('gdpr_exports'); }
};
