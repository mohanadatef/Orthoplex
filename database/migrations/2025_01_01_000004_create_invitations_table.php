<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration {
    public function up() {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->constrained('orgs')->onDelete('cascade');
            $table->string('email')->index();
            $table->string('token')->unique();
            $table->enum('role', ['owner','admin','member','auditor'])->default('member');
            $table->boolean('accepted')->default(false);            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() {
        Schema::dropIfExists('invitations');
    }
}
