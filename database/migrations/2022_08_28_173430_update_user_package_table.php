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
        Schema::table('user_package', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'user_id');
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
        Schema::table('user_package', function (Blueprint $table) {
            $table->renameColumn('user_id', 'owner_id');
            $table->dropTimestamps();
        });
    }
};
