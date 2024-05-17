<?php

namespace Diligentsquad\Reports\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullreportTable extends Migration
{
    public function up()
    {
       /* Schema::create('diligentsquad_reports_fullreport', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->timestamps();
        });*/
    }

    public function down()
    {
        Schema::dropIfExists('diligentsquad_reports_fullreport');
    }
}
