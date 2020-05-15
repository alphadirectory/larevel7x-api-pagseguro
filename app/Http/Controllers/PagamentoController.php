<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Providers\ServiceProvider;
use App\Models\PagamentosRegistrados;
use App\Models\PagseguroTransacoesStatus;
use PagSeguro\Library;
use PagSeguro\Configuration\Configure;
use PagSeguro\Domains\Requests\DirectPayment\Boleto;
use PagSeguro\Domains\Requests\DirectPayment\CreditCard;
use PagSeguro\Domains\Requests\DirectPayment\OnlineDebit;

class PagamentoController extends Controller
{
    public function __construct()
    {
        $this->setConfiguracoes();
        $this->service = new ServiceProvider;
    }

    public function session()
    {
        $sessao =  $this->service->getSessionId([
            'email' => $this->credenciais()->getEmail(),
            'token' => $this->credenciais()->getToken()
        ]);
        return response()->json(simplexml_load_string($sessao), 200);
    }

    public function monitorarTransacao($idPagamentoBancoLocal)
    {
        $pagamento = PagamentosRegistrados::find($idPagamentoBancoLocal);
        
        $transacao =  $this->service->getStatus([
            'email' => $this->credenciais()->getEmail(),
            'token' => $this->credenciais()->getToken(),
            'code' => json_decode($pagamento->log)->code
        ]);
        
        $transacao = simplexml_load_string($transacao);

        $status_db = PagseguroTransacoesStatus::fetchByStatus($transacao->status);

        return response()->json([
            'status_db' => $status_db,
            'transacao' => $transacao
        ], 200);
    }
    public function store(Request $request)
    {
        try {
            if ($request->tipo == "credito") {
                return response()->json(
                    $this->credito($request),
                    200
                );
            }
            if ($request->tipo == "boleto") {
                return response()->json(
                    $this->boleto($request),
                    200
                );
            }
            if ($request->tipo == "debito") {
                return response()->json(
                    $this->debito($request),
                    200
                );
            }
            throw new Exception("Modo de pagamento não indentificado");
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    private function boleto($request)
    {
        $boleto = new Boleto();
        $referencia = $this->setRefencias('boleto');
        $boleto = $this->setVariables($boleto, $request, $referencia);
        $result = $this->serialize(
            $boleto->register(
                $this->credenciais()
            ),
            true
        );
        PagamentosRegistrados::store($request, $referencia, json_encode($result));
        return [
            'transacionado' => true,
            'data' => $result
        ];
    }

    private function credito($request)
    {
        $referencia = $this->setRefencias('credito');
        $creditCard = new CreditCard();
        $creditCard->setReceiverEmail(env('PAGSEGURO_EMAIL'));
        $creditCard->setSender()->setHash($request->hash);
        $creditCard->setBilling()->setAddress()->withParameters(
            'Av. Brig. Faria Lima',
            '1384',
            'Jardim Paulistano',
            '01452002',
            'São Paulo',
            'SP',
            'BRA',
            'apto. 114'
        );
        $creditCard->setToken($request->cardToken);
        $creditCard->setInstallment()->withParameters(1, $request->amount);
        $creditCard->setHolder()->setName('comprador da silva');
        $creditCard->setHolder()->setPhone()->withParameters(85, 999556689);
        $creditCard->setHolder()->setDocument()->withParameters('CPF', '70408697059');
        $creditCard->setMode('DEFAULT');
        $creditCard = $this->setVariables($creditCard, $request, $referencia);
        $result = $this->serialize(
            $creditCard->register(
                $this->credenciais()
            )
        );
        PagamentosRegistrados::store($request, $referencia, json_encode($result));
        return [
            'transacionado' => true,
            'data' => $result
        ];
    }

    private function debito($request)
    {
        $referencia = $this->setRefencias('debito');
        $onlineDebit = new OnlineDebit();
        $onlineDebit->setMode('DEFAULT');
        $onlineDebit->setBankName($request->banco);
        $onlineDebit->setReceiverEmail(env('PAGSEGURO_EMAIL'));
        $onlineDebit = $this->setVariables($onlineDebit, $request, $referencia);
        $result = $this->serialize(
            $onlineDebit->register(
                $this->credenciais()
            )
        );
        PagamentosRegistrados::store($request, $referencia, json_encode($result));
        return    [
            'transacionado' => true,
            'data' => $result
        ];
    }

    private function credenciais()
    {
        return $this->configs->getAccountCredentials();
    }

    private function setConfiguracoes()
    {
        $this->configs = new Configure();
        $this->configs->setCharset('UTF-8');
        $this->configs->setAccountCredentials(
            env('PAGSEGURO_EMAIL'),
            (env('PAGSEGURO_AMBIENTE') == 'sandbox') ? env('PAGSEGURO_SANDBOX_TOKEN') : env('PAGSEGURO_TOKEN')
        );
        $this->configs->setEnvironment(env('PAGSEGURO_AMBIENTE'));
        $this->configs->setLog(true, storage_path('logs/pagseguro_' . date('Ymd') . '.txt'));
    }

    private function setRefencias($modo)
    {
        return date('dmY') . "-{$modo}-" . rand();
    }

    private function setVariables($data, $request, $referencia)
    {
        Library::initialize();
        Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
        Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");
        $data->setMode('DEFAULT');
        $data->setCurrency("BRL");
        $data->addItems()->withParameters('0001', 'nome do produto', 1, $request->amount);
        $data->setReference($referencia);
        $data->setSender()->setName('Nome do comprado');
        $data->setSender()->setEmail('bismarck@sandbox.pagseguro.com.br');
        $data->setSender()->setPhone()->withParameters(85, 999556689);
        $data->setSender()->setDocument()->withParameters('CPF', '70408697059');
        $data->setSender()->setHash($request->hash);
        $data->setSender()->setIp('127.0.0.0');
        $data->setShipping()->setAddress()->withParameters(
            'Av. Brig. Faria Lima',
            '1384',
            'Jardim Paulistano',
            '01452002',
            'São Paulo',
            'SP',
            'BRA',
            'apto. 114'
        );
        return $data;
    }

    private function serialize($data, $necessario_link = false)
    {
        $response = [
            'date' => $data->getDate(),
            'code' => $data->getCode(),
            'reference' => $data->getReference(),
            'type' => $data->getType(),
            'status' => $data->getStatus(),
            'lastEventDate' => $data->getLastEventDate(),
            'installmentCount' => $data->getInstallmentCount(),
            'cancelationSource' => $data->getCancelationSource(),
            'promoCode' => $data->getPromoCode(),
            'discountAmount' => $data->getDiscountAmount(),
            'escrowEndDate' => $data->getEscrowEndDate(),
            'extraAmount' => $data->getExtraAmount(),
            'feeAmount' => $data->getFeeAmount(),
            'grossAmount' => $data->getGrossAmount(),
            'netAmount' => $data->getNetAmount(),
            'itemCount' => $data->getItemCount(),
            'items' => $data->getItems(),
            'paymentMethod' => $data->getPaymentMethod(),
            'sender' => $data->getSender(),
            'shipping' => $data->getShipping(),
            'application' => $data->getApplication(),
            'creditorFees' => $data->getCreditorFees(),
        ];
        if ($necessario_link) $response['paymentLink'] = $data->getPaymentLink();
        return $response;
    }
}
