<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('login_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('org_id')->constrained('orgs');
            $table->timestamp('occurred_at')->index();
            $table->string('ip')->nullable();
            $table->string('ua')->nullable();
            $table->timestamps();
        });

        Schema::create('login_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('org_id')->constrained('orgs');
            $table->date('date')->index();
            $table->unsignedInteger('count')->default(0);
            $table->unique(['user_id','org_id','date']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('login_daily');
        Schema::dropIfExists('login_events');
    }
};
