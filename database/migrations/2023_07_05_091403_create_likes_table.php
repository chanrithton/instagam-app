<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
           // $table->bigInteger('post_id')->unsigned(); 
           // $table->bigInteger('user_id')->unsigned();
            $table->foreignId('post_id')->references('id')->on('posts'); // add foreign key
            $table->foreignId('user_id')->references('id')->on('users'); // add foreign key
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
        Schema::dropIfExists('likes');
    }
}
