<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTrashTable extends Migration
{
    public function up()
    {
        Schema::create('trash', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->integer('age');
            $table->timestamp('deleted_at')->nullable(); // To record when the entry was deleted
            $table->timestamps();
        });
    }


}
