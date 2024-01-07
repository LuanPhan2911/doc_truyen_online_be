<?php

use App\Enums\StoryUserType;
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
            $table->tinyInteger("type")->after("index")->default(StoryUserType::Reading)->comment("StoryUserTypeEnum");
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
            //
        });
    }
};
