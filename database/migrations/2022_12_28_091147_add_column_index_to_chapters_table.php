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
        if (!Schema::hasColumn('chapters', 'index')) //check the column
        {
            Schema::table('chapters', function (Blueprint $table) {
                $table->addColumn('integer', 'index')->after('content');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('chapters', 'index')) //check the column
        {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropColumn('index');
            });
        }
    }
};
