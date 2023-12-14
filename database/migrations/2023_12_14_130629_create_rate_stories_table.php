<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained();
            $table->float('characteristic');
            $table->float('plot');
            $table->float('world_building');
            $table->float('quality_convert');
            $table->foreignId('comment_id')->constrained();
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
        Schema::dropIfExists('rate_stories');
    }
};
