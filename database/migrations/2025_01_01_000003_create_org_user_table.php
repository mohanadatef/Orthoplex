<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrgUserTable extends Migration {
    public function up() {
        Schema::create('org_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->constrained('orgs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['org_id','user_id']);
            $table->softDeletes();
        });
    }
    public function down() {
        Schema::dropIfExists('org_user');
    }
}
