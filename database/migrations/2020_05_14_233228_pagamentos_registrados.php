<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PagamentosRegistrados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos_registrados', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement()->index();
            $table->string('referencia');
            $table->longText('log')->nullable();
            $table->string('cartao_final');
            $table->string('cartao_bandeira');
            $table->string('tipo');
            $table->string('valor');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagamentos_registrados');
    }
}
