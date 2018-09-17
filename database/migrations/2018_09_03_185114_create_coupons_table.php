<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned()->index();
            $table->integer('organiser_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('event_id')->unsigned()->index();
            $table->string('code')->unique();
            $table->string('type');
            $table->integer('value')->nullable();
            $table->integer('percent_off')->nullable();
            $table->integer('limit_number')->nullable();
            $table->timestamp('end_date');
            $table->tinyInteger('is_active')->default(1);
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
        Schema::drop('coupons');

    }
}
