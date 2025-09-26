<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginDailyTable extends Migration {
    public function up() {
        Schema::create('login_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('count')->default(0);
            $table->timestamps();
            $table->unique(['date','user_id']);
            $table->softDeletes();
        });
    }
    public function down() {
        Schema::dropIfExists('login_daily');
    }
}
