<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationPercentageAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_percentage_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained();
            $table->foreignId('coin_id')->index()->constrained();
            $table->foreignId('currency_id')->index()->constrained();
            $table->string('period');
            $table->decimal('threshold', 5, 2);
            $table->timestamps();

            $table->index(['coin_id', 'currency_id', 'period', 'threshold']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_percentage_alerts');
    }
}
