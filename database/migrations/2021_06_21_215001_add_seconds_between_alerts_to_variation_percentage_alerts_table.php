<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondsBetweenAlertsToVariationPercentageAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variation_percentage_alerts', function (Blueprint $table) {
            $table->unsignedBigInteger('seconds_between_alerts')
                ->default(config('app.cache_ttls.variation_percentage_notification'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_percentage_alerts', function (Blueprint $table) {
            $table->dropColumn('seconds_between_alerts');
        });
    }
}
