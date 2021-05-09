<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('averages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->index()->constrained();
            $table->foreignId('currency_id')->index()->constrained();
            $table->unsignedBigInteger('value');
            $table->datetime('from');
            $table->datetime('to');
            $table->timestamps();

            $table->index(['from', 'to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('averages');
    }
}
