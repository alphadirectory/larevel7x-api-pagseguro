<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagamentosRegistrados extends Model
{
    protected $fillable = [
        'referencia',
        'log',
        'cartao_final',
        'cartao_bandeira',
        'tipo',
        'valor'
    ];

    public static function store($request, $referencia, $log)
    {
        $cartao = explode(" ", $request->cardNumber);
        return self::create([
            'referencia' => $referencia,
            'log' => $log,
            'cartao_final' => end($cartao),
            'cartao_bandeira' => $request->cardBrand['name'],
            'tipo' => $request->tipo,
            'valor' => $request->amount
        ]);
    }
}
