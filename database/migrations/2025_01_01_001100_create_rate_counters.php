<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rate_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->string('route');
            $table->date('date');
            $table->unsignedInteger('count')->default(0);
            $table->unique(['org_id','route','date']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('rate_counters'); }
};
