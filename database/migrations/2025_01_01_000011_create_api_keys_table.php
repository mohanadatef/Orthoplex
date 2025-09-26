<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiKeysTable extends Migration {
    public function up() {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('grace_until')->nullable();
            $table->string('secret')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() {
        Schema::dropIfExists('api_keys');
    }
}
