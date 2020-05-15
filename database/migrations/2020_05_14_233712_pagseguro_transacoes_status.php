<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PagseguroTransacoesStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagseguro_transacoes_status', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement()->index();
            $table->string('status');
            $table->string('text_amigavel');
            $table->string('pag_seguro_text');
            $table->longText('descricao');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        $seeds = $this->seed();

        foreach ($seeds as $seed) {
            DB::table('pagseguro_transacoes_status')->insert($seed);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagseguro_transacoes_status');
    }

    private function seed()
    {
        $data[] = [
            'status' => 1,
            'pag_seguro_text' => 'Aguardando',
            'descricao' => 'pagamento	O comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.',
            'text_amigavel' =>  'Em análise'
        ];

        $data[] = [
            'status' => 2,
            'pag_seguro_text' => 'Em análise',
            'descricao' => 'O comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.',
            'text_amigavel' =>  'Em análise'
        ];

        $data[] = [
            'status' => 3,
            'pag_seguro_text' => 'Paga',
            'descricao' => 'A transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.',
            'text_amigavel' =>  'Pagamento registrado com sucesso'
        ];

        $data[] = [
            'status' => 4,
            'pag_seguro_text' => 'Disponível',
            'descricao' => 'A transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.',
            'text_amigavel' =>  'Disponivel para pagamento'
        ];

        $data[] = [
            'status' => 5,
            'pag_seguro_text' => 'Em disputa',
            'descricao' => ' comprador, dentro do prazo de liberação da transação, abriu uma disputa.',
            'text_amigavel' =>  'Em disputa'
        ];

        $data[] = [
            'status' => 6,
            'pag_seguro_text' => 'Devolvida',
            'descricao' => 'O valor da transação foi devolvido para o comprador.',
            'text_amigavel' =>  'Valor devolvido pra o comprador'
        ];

        $data[] = [
            'status' => 7,
            'pag_seguro_text' => 'Cancelada',
            'descricao' => '',
            'text_amigavel' =>  'O pagamento foi cancelado'
        ];

        return $data;
    }
}
