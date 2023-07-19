<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            //$table->auto_increment();
            $table->string('text');
           // $table->bigInteger('post_id')->unsigned(); // post that the comment belongs to
           // $table->bigInteger('user_id')->unsigned(); // who wrote the comment
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
        Schema::dropIfExists('comments');
    }
}
