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
            $table->timestamp('reading_deleted_at')->nullable();
            $table->timestamp('marking_deleted_at')->nullable();
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
            $table->dropColumn('reading_deleted_at');
            $table->dropColumn('marking_deleted_at');
        });
    }
};
