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
        Schema::table('story_user', function (Blueprint $table) {
            $table->tinyInteger('marked')->default(0);
            $table->integer('marked_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('story_user', function (Blueprint $table) {
            $table->dropColumn('marked');
            $table->dropColumn('marked_index');
        });
    }
};
