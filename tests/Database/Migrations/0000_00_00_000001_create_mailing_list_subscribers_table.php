<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailingListSubscribersTable extends Migration
{
    public function up()
    {
        Schema::create('mailing_list_subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('mailing_list_id');
            $table->string('email_address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mailing_list_subscribers');
    }
}
