<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('email')->nullable();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['Individual', 'Professional' ,'Administrator'])->default('Individual');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            /*$ table->rememberToken();  it's not supposed to be used to authenticate. It's used by the framework 
            to help against Remember Me cookie hijackingz. The value is refreshed upon login and logout. 
            If a cookie is hijacked by a malicious person, logging out makes the hijacked cookie useless 
            since it doesn't match anymore.*/
            $table->rememberToken();
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
