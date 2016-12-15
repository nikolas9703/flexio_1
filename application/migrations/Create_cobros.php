<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cob_cobros', function (Blueprint $table) {
            $table->increments('id');
            $table->binary('uuid_cobro');
            $table->string('codigo');
            $table->string('estado');
            $table->string('metodo_pago');
            $table->decimal('total_pago', 5, 2);
            $table->string('referencia')->nullable();
            $table->dateTime('fecha_pago');
            $table->integer('cliente_id');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cob_cobros');
    }
}
