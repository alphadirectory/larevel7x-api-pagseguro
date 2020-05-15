<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagseguroTransacoesStatus extends Model
{
    public $table = "pagseguro_transacoes_status";
    
    public static function fetchByStatus($status)
    {
        return self::where('status', $status)->get();
    }
}
