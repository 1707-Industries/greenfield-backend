<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyAuthConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_auth_connections', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('provider');

            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->string('token_secret')->nullable();
            $table->integer('expires_in')->nullable();
            $table->string('oauth_version')->nullable();

            $table->string('provider_id');
            $table->string('provider_name')->nullable();
            $table->string('provider_nickname')->nullable();
            $table->string('provider_email')->nullable();
            $table->string('provider_avatar')->nullable();

            $table->json('provider_user');

            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'provider',
                'provider_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_party_auth_connections');
    }
}
