<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
			
			$table->string('firstname')->nullable();
			$table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->string('password');
			$table->string('phone');
			
			

			$table->string('email_verified_at')->nullable();
			$table->string('street_address')->nullable();
			$table->string('division_id')->nullable();
			$table->string('district_id')->nullable();
			$table->string('ip_address')->nullable();
			$table->string('avatar')->nullable();
			$table->string('status')->nullable();
			$table->string('is_deleted')->nullable();
			$table->string('remember_token')->nullable();

            $table->timestamps();
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
}
