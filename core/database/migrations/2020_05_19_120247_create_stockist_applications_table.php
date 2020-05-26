<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockistApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockist_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('country');
            $table->string('email');
            $table->string('mobile');
            $table->string('state');
            $table->string('city');
            $table->string('address');
            $table->integer('bank_id');
            $table->string('account_number');
            $table->string('status')->default('Pending');
            $table->string('zip')->nullable(true);
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
        Schema::dropIfExists('stockist_applications');
    }
}