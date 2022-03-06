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
        Schema::create('rencana_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembiayaan_id');
            $table->date('payment_date');
            $table->integer('pokok');
            $table->integer('margin');
            $table->timestamps();

            $table->foreign('pembiayaan_id')->references('id')->on('pembiayaans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rencana_pembayarans');
    }
};
