<?php

namespace Igniter\Frontend\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePrimaryKeyBigintAllTables extends Migration
{
    public function up()
    {
        Schema::table('igniter_frontend_banners', function (Blueprint $table) {
            $table->unsignedBigInteger('banner_id', TRUE)->change();
        });

        Schema::table('igniter_frontend_sliders', function (Blueprint $table) {
            $table->unsignedBigInteger('id', TRUE)->change();
        });

        Schema::table('igniter_frontend_subscribers', function (Blueprint $table) {
            $table->unsignedBigInteger('id', TRUE)->change();
        });
    }

    public function down()
    {
    }
}
