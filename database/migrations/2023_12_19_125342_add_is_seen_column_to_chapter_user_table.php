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
        Schema::table('chapter_user', function (Blueprint $table) {
            $table->boolean('is_seen')->default(0)->after('chapter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_user', function (Blueprint $table) {
            $table->dropColumn('is_seen');
        });
    }
};
