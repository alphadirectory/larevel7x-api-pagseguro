<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/pagamento', 'PagamentoController@store');
Route::get('/sessaoid', 'PagamentoController@session');
Route::get('/status/{idPagamentoBancoLocal}', 'PagamentoController@monitorarTransacao');

