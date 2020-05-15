<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\Facades\Http;

class ServiceProvider
{

    public function getSessionId($paylod)
    {
        $url = $this->setHots();
        $url .= '/v2/sessions?';
        $url .= "&email={$paylod['email']}";
        $url .= "&token={$paylod['token']}";

        $response = Http::post($url);
        
        if (!$response->ok()) throw new Exception('Erro de comunicação com a api do pagSeguro');
        
        return $response->body();
    }

    public function getStatus($paylod)
    {

        $url = $this->setHots();
        $url .= "/v3/transactions/{$paylod['code']}";
        $url .= "?email={$paylod['email']}";
        $url .= "&token={$paylod['token']}";

        $response = Http::get($url);
        
        if (!$response->ok()) throw new Exception('Erro de comunicação com a api do pagSeguro');
        
        return $response->body();
    
    }



    private function setHots()
    {
        if (env('PAGSEGURO_AMBIENTE') == 'sandbox') {
            return "https://ws.sandbox.pagseguro.uol.com.br";
        } else {
            return "https://ws.pagseguro.uol.com.br";
        }
    }
}
