<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->integer('status');
            $table->integer('user_assigned_id')->unsigned();
            $table->foreign('user_assigned_id')->references('id')->on('users');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('user_created_id')->unsigned();
            $table->foreign('user_created_id')->references('id')->on('users');
            $table->text('original_debt');
            $table->integer('program_length');
            $table->integer('repayment_percent');
            $table->text('payment_date');
            $table->datetime('enrollment_date');
            $table->text('first_payment_date')->nullable();
            $table->integer('payment_sched_multiple');
            $table->integer('admin_fee_months');
            $table->integer('admin_fee_percent');
            $table->integer('service_fee_percent');
            $table->text('payment_schedule');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('debts');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
