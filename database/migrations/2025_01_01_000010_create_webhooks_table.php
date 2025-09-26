<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration {
    public function up() {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('event');
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->text('last_error')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() {
        Schema::dropIfExists('webhooks');
    }
}
