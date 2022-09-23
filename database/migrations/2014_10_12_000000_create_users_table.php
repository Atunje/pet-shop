<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id', true);
            $table->uuid('uuid')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_admin');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->text('address');
            $table->string('phone_number')->unique();
            $table->boolean('is_marketing');
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['uuid', 'is_admin']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
